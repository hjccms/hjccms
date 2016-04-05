<?php
/*
 * 后台登陆 
 */
class LoginAction  extends Action 
{
    /*
     * 登陆页面
     */
    function index()
    {
        if(session('hashuser')&&session('hashuser')==md5(encrypt(session('username').cookie('PHPSESSID'),'E',C('APP_KEY').session('adminId'))))
        {
            redirect(U('/Admin/Index'));
            die();
        }
        else
        {
            //清掉所有session和管理员cookie信息
            session(null);
            cookie('adminInfo',null);
        }
 
        $this->display();
    }
    //ajax的登陆
    function ajaxLogin()
    {
        if(!IS_AJAX) die('非法请求！');
        $username = $this->_post('username');
        $password = $this->_post('password');
        $verify = $this->_post('verify');
        if($username==''||$password=='') $this->ajaxReturn('','请输入账号和密码！',0);
        if($verify=='') $this->ajaxReturn('','请输入验证码！',0);
        if(md5($verify)!=session("verify")) $this->ajaxReturn('','验证码错误！',0);
        $password = md5(encrypt($password, 'E'));
        $condition =  array('username'=>$username,'password'=>$password);
        $adminInfo = D('Admin')->getAdminInfo($condition);
        if(!$adminInfo) $this->ajaxReturn('','账号密码错误！',0);
        if($adminInfo['valid']!=1) $this->ajaxReturn('','管理员已被禁用！',0);
        session('username',$username);
        session('adminId',$adminInfo['id']);
        session('hashuser',md5(encrypt($username.cookie('PHPSESSID'),'E',C('APP_KEY').$adminInfo['id'])));
        //更新登陆信息
        $data['last_login_time'] = time();
        $data['id'] = $adminInfo['id'];
        $data['last_login_ip'] =  get_client_ip();
        D('Admin')->updateAdminInfo($data);
        $this->ajaxReturn('','验证成功！',1);
    }
    //退出
    function loginOut()
    {
        session(null);
        cookie('adminInfo',null);
        redirect(U('/Admin/Login'));
    }
    /**
     * 生成验证码
     */
    function getVerify()
    {
        import('@.Extend.Image');
        Image::buildImageVerify(4,1,'gif',114,46);
    }
}
