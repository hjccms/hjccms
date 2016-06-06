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
    
    function getAccessToken() {
        $site_id = $this->adminInfo->site_id;
        $appid = 'wxcd0f7466d831e280';
        $appsecret = 'a59adc20e4234f0362221144e3e5b96c';
        if(!$site_id || !$appid || !$appsecret){
            return false;
        }
        $file_url = str_replace("{site_id}",$site_id,C('WEIXIN_ACCESS_TOKEN'));
        $token_json = file_get_contents($file_url);
        if($token_json){
            $result = json_decode($token_json, true);
            if($result->expires_in < time()) {
                $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$appsecret}";
                $res = json_decode(curlGet($url), true);
                $access_token = $res->access_token;  //重新获取的access_token
                if($access_token){
                    $data->site_id = $site_id;
                    $data->expires_in = time() + 7000;
                    $data->access_token = $access_token;
                    file_put_contents($file_url, json_encode($data));
                }
            }else{
                $access_token = $result->access_token;
            }
        }
        return $access_token;
    }
}
