<?php
/*
 * 后台控制器基础类
 */
class BaseAction  extends Action 
{
    var $adminInfo;
    function __construct() 
    {
        parent::__construct();
        //验证登陆状态
        if(session('hashuser')&&session('hashuser')==$this->hashUser())
        {
            //设置管理员信息
            if(cookie('adminInfo'))
            {
                $this->adminInfo = cookie('adminInfo');
            }
            else
            {
                $adminInfo = D('Admin')->getAdminInfo('id='.session('adminId'));
                cookie('adminInfo',$adminInfo,3600*7);//保存时间足够一次不间断的操作
                $this->adminInfo = cookie('adminInfo');
            }
            $this->assign('adminInfo',$this->adminInfo);
        }
        else
        {
            //去登陆
            redirect(U('/Admin/Login/index'));
        }
    }
    function hashUser()
    {
        return md5(encrypt(session('username').cookie('PHPSESSID'),'E',C('APP_KEY').session('adminId')));
    }
}

