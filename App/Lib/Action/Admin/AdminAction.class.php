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
        
        load("@.form");
        $this->display();
    }
}
