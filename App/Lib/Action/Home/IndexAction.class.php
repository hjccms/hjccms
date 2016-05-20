<?php
// 本类由系统自动生成，仅供测试用途
class IndexAction extends Action {
    public function index(){
        C('SHOW_PAGE_TRACE',false);
        $this->display();
    }
    
    function checkLevel()
    {
        C('SHOW_PAGE_TRACE',false);
        $this->display();
    }
    function testLogin()
    {
        C('SHOW_PAGE_TRACE',false);
        $this->display();
    }
    function testResult()
    {
        C('SHOW_PAGE_TRACE',false);
        $this->display();
    }
    
    function course()
    {
        C('SHOW_PAGE_TRACE',false);
        $this->display();
    }
    
    function article()
    {
        C('SHOW_PAGE_TRACE',false);
        $id = $this->_get("id");
        $site_id = 1;
        $info  = D("Sys_article")->where("site_id={$site_id} and valid=1")->order("sort asc")->select();
        $this->assign('id',$id);
        $this->assign('info',$info);
        $this->display();
    }
}