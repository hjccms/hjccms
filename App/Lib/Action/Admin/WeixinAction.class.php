<?php

/*
 * 主要放 检查token 接收消息等不需要管理员操作 程序自动完成的业务
 * 
 * 
 */


class WeixinAction extends Action{
    
    function index(){
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
}
