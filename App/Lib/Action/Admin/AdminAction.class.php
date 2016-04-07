<?php
/*
 * 管理员控制器
 */
class AdminAction  extends BaseAction 
{
    function index()
    {
        $this->getContentButton();
        $this->getListButton();
        $this->display();
    }
    
    function adminAdd()
    {
        //调用角色
        $site_id = $this->adminInfo->site_id;
        if($site_id>1) $_string = " id != 1"; else $_string = '';
        $roles = D('Role')->getRole('1',$site_id,$_string);
        $this->assign('roles',$roles);
        load("@.form");
        $this->display();
    }
    
    
}
