<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * 主要需要管理员主动去触发的业务 需要验证管理员的权限
 *
 * @author hujianchuang
 */
class WeixinBaseAction extends BaseAction{
    
    function getAccessToken(){
        $site_id = $this->adminInfo->site_id;
        $appid = encrypt($this->adminSiteInfo->appid,'D',C('APP_KEY'));
        $appsecret = encrypt($this->adminSiteInfo->appsecret,'D',C('APP_KEY'));
        if(!$site_id || !$appid || !$appsecret){
            return false;
        }
        $access_token = cookie("adminSiteAccessToken");
        $access_token = json_decode(encrypt($access_token,'D',C('APP_KEY')));
        if(!$access_token){
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$appsecret}";
            $res = json_decode(curlGet($url));
            if($res->access_token){
                $data['site_id'] = $site_id;
                $data['access_token'] = $res->access_token;
                cookie('adminSiteAccessToken', encrypt(json_encode($data),'E',C('APP_KEY')), $res->expires_in);
                $access_token = $res->access_token;
            }
        }else{
            $access_token = $access_token->access_token;
        }
        return $access_token;
    }

    public function responseMsg()
    {
		//get post data, May be due to the different environments
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

      	//extract post data
		if (!empty($postStr)){
                /* libxml_disable_entity_loader is to prevent XML eXternal Entity Injection,
                   the best way is to check the validity of xml by yourself */
                libxml_disable_entity_loader(true);
              	$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
                $fromUsername = $postObj->FromUserName;
                $toUsername = $postObj->ToUserName;
                $keyword = trim($postObj->Content);
                $time = time();
                $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";             
				if(!empty( $keyword ))
                {
              		$msgType = "text";
                	$contentStr = "Welcome to wechat world!";
                	$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                	echo $resultStr;
                }else{
                	echo "Input something...";
                }

        }else {
        	echo "";
        	exit;
        }
    }
}
