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
        $result = actionPost('http://hjc.student.haowj.com/Home/Api/ajaxRegUser', $data);
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
