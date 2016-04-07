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
        load("@.form");
        $this->display();
    }
}
