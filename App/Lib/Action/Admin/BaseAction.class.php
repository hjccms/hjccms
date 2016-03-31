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
            $this->getPosition();
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
    
    //获取位置
    function getPosition(){
        $id = $this->getMenuId();
        if(!$id) return false;
        $position = D("Menu")->getPositionById($id);
        $this->assign('position',$position);
    }
    
    //获取内容按钮
    function getContentButton(){
        $id = $this->getMenuId();
        if(!$id) return false;
        $menu = D('Menu')->getMenu($id,true,false,2);
        $contentButton = D('Menu')->getContentButton($menu);
        $this->assign('contentButton',$contentButton);
    }
    
    //获取列表按钮
    function getListButton(){
        $id = $this->getMenuId();
        if(!$id) return false;
        $menu = D('Menu')->getMenu($id,true,false,3);
        $listButton = D('Menu')->getListButton($menu);
        $this->assign('listButton',$listButton);
    }
    
    //获取菜单id
    function getMenuId(){
        $id = D('Menu')->getIdByUrl(str_replace("Action", "", MODULE_NAME),ACTION_NAME,get_param());
        return $id;
    }
}

