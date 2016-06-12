<?php

/*
 * 主要放 检查token 接收消息等不需要管理员操作 程序自动完成的业务
 * 
 * 
 */


class WeixinAction extends Action{
    
    
    //验证
    function valid(){
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        if($postStr){
            $this->responseMsg();exit;
        }
        $token = $this->_get("token");
        $echoStr = $_GET["echostr"];
        if($this->checkSignature($token)){
        	echo $echoStr;
        	exit;
        } 
    }
        
    private function checkSignature($token){
        if(!$token){
            throw new Exception('TOKEN is not defined!');
        }
    
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        		
		$token = $token;
		$tmpArr = array($token, $timestamp, $nonce);
        
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode($tmpArr);
		$tmpStr = sha1( $tmpStr );
		
		if($tmpStr == $signature){
			return true;
		}else{
			return false;
		}
	}
    
    public function responseMsg(){
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
		if (!empty($postStr)){
            libxml_disable_entity_loader(true);
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $siteInfo = D("Weixin_config")->getInfo("ghid='{$postObj->ToUserName}'");
            $msg = D("Weixin_msg")->getInfo("site_id={$siteInfo['site_id']} and keyword like '%".$postObj->Content."%' and valid=1 and del is null");
            if($msg){
                if($msg['keyword_type'] == 1){//完全匹配
                    if($postObj->Content == $msg['keyword']){
                        $data['to_user_name'] = $postObj->FromUserName;
                        $data['from_user_name'] = $postObj->ToUserName;
                        $data['msg_type'] = $msg['msg_type'];
                        $data['content'] = $msg['content'];
                        echo $this->sendMsg($data);
                    }
                }else{//模糊匹配
                    $data['to_user_name'] = $postObj->FromUserName;
                    $data['from_user_name'] = $postObj->ToUserName;
                    $data['msg_type'] = $msg['msg_type'];
                    $data['content'] = $msg['content'];
                    echo $this->sendMsg($data);
                }
            }         
        }
    }
    
    //发送被动响应消息
	function sendMsg($data) {
		$textTpl = "<xml>
            <ToUserName><![CDATA[{$data['to_user_name']}]]></ToUserName>
            <FromUserName><![CDATA[{$data['from_user_name']}]]></FromUserName>
            <CreateTime>%s</CreateTime>
            <MsgType><![CDATA[{$data['msg_type']}]]></MsgType>";

		switch ($data['msg_type']) {
			case 'text':
				$textTpl .=
					"<Content><![CDATA[{$data['content']}]]></Content>";
				break;
			case 'image':
				$textTpl .=
					"<Image>
                    <MediaId><![CDATA[{$data['media_id']}]]></MediaId>
                    </Image>";
				break;
			case 'voice':
				$textTpl .=
					"<Voice>
                    <MediaId><![CDATA[{$data['media_id']}]]></MediaId>
                    </Voice>";
				break;
			case 'video':
				$textTpl .=
					"<Video>
                    <MediaId><![CDATA[{$data['media_id']}]]></MediaId>
                    <Title><![CDATA[{$data['title']}]]></Title>
                    <Description><![CDATA[{$data['content']}]]></Description>
                    </Video> ";
				break;
			case 'music':
				$textTpl .=
					"<Music>
                    <Title><![CDATA[{$data['title']}]]></Title>
                    <Description><![CDATA[{$data['content']}]]></Description>
                    <MusicUrl><![CDATA[{$data['url']}]]></MusicUrl>
                    <HQMusicUrl><![CDATA[{$data['url2']}]]></HQMusicUrl>
                    <ThumbMediaId><![CDATA[{$data['thumb_media_id']}]]></ThumbMediaId>
                    </Music>";
				break;
			case 'news':
				$titles = explode(',', $data['title']);
				$count = count($titles);
				$contents = explode(',', $data['content']);
				$urls = explode(',', $data['url']);
				$url2s = explode(',', $data['url2']);
				$textTpl .=
					"<ArticleCount>{$count}</ArticleCount>
                    <Articles>";
				foreach ($titles as $key => $title) {
					$textTpl .=
					"<item>
                        <Title><![CDATA[{$title}]]></Title> 
                        <Description><![CDATA[{$contents[$key]}]]></Description>
                        <PicUrl><![CDATA[{$urls[$key]}]]></PicUrl>
                        <Url><![CDATA[{$url2s[$key]}]]></Url>
                    </item>";
				}
				$textTpl .=
					"</Articles>";
				break;
			default:
				break;
		}
		$textTpl .= "</xml>";
		return sprintf($textTpl, time());
	}
}
