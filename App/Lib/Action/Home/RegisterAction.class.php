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
            $this->ajaxReturn('',$ret->info,'1');
        }
        
    }
}
