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
    
    function ajaxAddUser(){
        
        $data['name'] = $this->_post('name');
        $data['age'] = $this->_post('age');
        $data['mobile'] = $this->_post('mobile');
        $data['area'] = $this->_post('area');
        $data['site_id'] = $this->siteInfo->id;
        $data['channel'] = '官网';
        $data['origin'] = $this->_post('origin');
        $data['create_time'] = time();
        $info = D("Student_temp")->getInfo("site_id={$this->siteInfo->id} and  mobile='{$data['mobile']}' and origin='{$data['origin']}'");
        if($info){
            $re = D('Student_temp')->where("id={$info['id']}")->save($data);
        }else{
            $re = D('Student_temp')->add($data);
        }
        if($re){
            $data2['mobile'] = $this->_post('mobile');
            $data2['name'] = $this->_post('name');
            $data2['channel'] = '官网';
            $data2['origin'] = $this->_post('origin');
            $data2['other']  = json_encode(array('age'=>$this->_post('age'),'area'=>$this->_post('area')));
            $data2['appid']    = 'OMNI';
            $data2['appkey']   = 'OMNILOGIN';
            $data2['time'] = time();
            $data2['sign'] = md5($data2['appid'].$data2['appkey'].$data2['mobile'].$data2['time']);
            $result = actionPost(C('DS_URL').'/addUser', $data2);
            $ret = json_decode($result);
            $this->ajaxReturn(base64_encode(json_encode(array('name'=>$this->_post('name'),'mobile'=>$this->_post('mobile')))),base64_encode(json_encode(array('name'=>$this->_post('name'),'mobile'=>$this->_post('mobile')))),1);
        }else{
            $this->ajaxReturn('','操作失败',0);
        }
    }
}
