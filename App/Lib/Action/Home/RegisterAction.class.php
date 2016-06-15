<?php
class RegisterAction extends BaseAction {
    
    function login()
    {
        $appid= 'OMNI';
        $appkey = 'OMNILOGIN';
        
        $time = time();
        $sign = md5($appid.$appkey.$time);
        
        $this->assign('appid',$appid);
        $this->assign('time',$time);
        $this->assign('sign',$sign);
        $this->assign('loginapiurl',C('LOGINAPIURL'));
        $this->display();
    }
    function reg()
    {
        $this->display();
    }
    function ajaxLoginPost()
    {
        $post = $this->_post();
        $data['username'] = $post['username'];
        $data['password'] = md5($post['password']);
        $data['appid']    = 'OMNI';
        $data['appkey']   = 'OMNILOGIN';
        $data['time'] = time();
        $data['sign'] = md5($data['appid'].$data['appkey'].$data['time']);
        $result = actionPost(C('LOGINAPIURL'), $data);
        $ret = json_decode($result);
        if($ret->data=='error')
        {
            $this->ajaxReturn('',$ret->info,'0');
        }
        if($ret->data=='success')
        {
            $arr = explode('?sign=', $ret->info);
            $baseSign = decrypt($arr['1'], 'OUTSCHOOLAPI');
            $info = explode('&',$baseSign);
            
            $userInfo['id'] = $info['1'];
            $userInfo['username'] = $data['username'];
            $userInfo['centerUrl']= $ret->info;
            cookie('userInfo',$userInfo,3600*5);
            session('levelTest',null);
            session('hashLevelTest',null);
            $this->ajaxReturn('',$ret->info,'1');
        }
        
    }
    function ajaxPost()
    {
        $post = $this->_post();
        $data['username'] = $post['username'];
        $data['password'] = $post['password'];
        $data['appid']    = 'OMNI';
        $data['appkey']   = 'OMNILOGIN';
        
        $data['time'] = time();
        $data['sign'] = md5($data['appid'].$data['appkey'].$data['username'].$data['time']);
        $result = actionPost(C('REGISTERAPIURL'), $data);
        $ret = json_decode($result);
        if($ret->data=='error')
        {
            $this->ajaxReturn('',$ret->info,'0');
        }
        if($ret->data=='success')
        {
            $arr = explode('?sign=', $ret->info);
            $baseSign = decrypt($arr['1'], 'OUTSCHOOLAPI');
            $info = explode('&',$baseSign);
            
            $userInfo['id'] = $info['1'];
            $userInfo['username'] = $data['username'];
            $userInfo['centerUrl']= $ret->info;
            cookie('userInfo',$userInfo,3600*5);
            session('levelTest',null);
            session('hashLevelTest',null);
            $this->ajaxReturn('',$ret->info,'1');
        }
        
    }
}
