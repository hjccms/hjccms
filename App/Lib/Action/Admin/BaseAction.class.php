<?php
/*
 * 后台控制器基础类
 */
class BaseAction  extends Action 
{
    var $adminInfo;
    var $menuId;
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
                if(isset($adminInfo['role_id'])){
                    $roleInfo = D("Role")->getRoleInfo("id=".$adminInfo['role_id']);
                    $adminInfo['menu_ids'] = encrypt($roleInfo['menu_ids'],'E',C('APP_KEY'));
                }
                cookie('adminInfo',$adminInfo,3600*7);//保存时间足够一次不间断的操作
                $this->adminInfo = cookie('adminInfo');
            }
            $this->assign('adminInfo',$this->adminInfo);
            $this->menuId = $this->getMenuId();
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
        if(!$this->menuId) return false;
        $position = D("Menu")->getPositionById($this->menuId);
        $this->assign('position',$position);
    }
    
    //获取内容按钮
    function getContentButton(){
        if(!$this->menuId) return false;
        $menu = D('Menu')->getMenu($this->menuId,true,false,2);
        $contentButton = D('Menu')->getContentButton($menu);
        $this->assign('contentButton',$contentButton);
    }
    
    //获取列表按钮
    function getListButton(){
        if(!$this->menuId) return false;
        $listButton = D('Menu')->getMenu($this->menuId,true,false,3);
        $this->assign('listButton',$listButton);
    }
    
    //获取菜单id
    function getMenuId(){
        $id = D('Menu')->getIdByUrl(str_replace("Action", "", MODULE_NAME),ACTION_NAME,get_param());
        return $id;
    }
}

