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
}