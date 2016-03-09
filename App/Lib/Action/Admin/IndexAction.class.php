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
        //$result = D('Menu')->getMenu(0,true,false);
        //$this->assign('result',$result);
        C('SHOW_PAGE_TRACE',false);
        $this->display();
    }
    
    function left()
    {
        C('SHOW_PAGE_TRACE',false);
        $this->display();
    }
}
