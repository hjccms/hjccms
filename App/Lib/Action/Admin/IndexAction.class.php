<?php
/*
 * 后台首页控制器
 */
class IndexAction  extends BaseAction 
{
    function index()
    {
        $this->display();
    }
    
    function main()
    {
        $this->display();
    }
    
    function top()
    {
        C('SHOW_PAGE_TRACE',false);
        $result = D('Menu')->getMenu(0,true,false);
        $menuTree = D('Menu')->getTopTree($result);
        $this->assign('menuTree',$menuTree);
        $this->display();
    }
    
    function left()
    {
        $id = $this->_get('id');
        $id = $id?$id:1;
        $result = D('Menu')->getMenu($id,true,true,1);
        $menuTree = D('Menu')->getLeftTree($result);
        $this->assign('menuTree',$menuTree);
        $this->assign('name',D("Menu")->getNameById($id));
        C('SHOW_PAGE_TRACE',false);
        $this->display();
    }
    
    //位置
    function position(){
        $this->display();
    }
}
