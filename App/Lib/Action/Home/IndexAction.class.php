<?php
// 本类由系统自动生成，仅供测试用途
class IndexAction extends BaseAction {
    public function index()
    {
        $this->display();
    }
    
    function checkLevel()
    {
        if(!session('hashLevelTest') || !(session('hashLevelTest')==md5(encrypt(session('levelTest').cookie('PHPSESSID'),'E',C('APP_KEY'))))){
            redirect(U('/Index/testLogin'));
        }
        $obj = json_decode(session('levelTest'));
        $info = D("Listening_test")->where("`name`='{$obj->name}' and mobile='{$obj->mobile}' and site_id={$this->siteInfo->id}")->order('id desc')->select();
        if($info){
            if($info[0]['level'] != '0'){
                redirect(U('/Index/testResult'));
            }
            if($info[0]['level'] == '0' && $info[0]['schedule'] != ''){
                $schedule = json_decode($info[0]['schedule'],true);
                $info[0]['end_key'] = end(array_keys($schedule));
                $info[0]['end_value'] = end(array_values($schedule));
                $this->assign('info',$info[0]);
            }
        }else{
            $info[0]['schedule'] = 0;
            $info[0]['end_key'] = 0;
            $info[0]['end_value'] = 0;
        }
        $this->assign('info',$info[0]);
        $this->display();
    }
    function testLogin()
    {
        $this->display();
    }
    function testResult()
    {
        $obj = json_decode(session('levelTest'));
        $info = D("Listening_test")->where("`name`='{$obj->name}' and mobile='{$obj->mobile}' and site_id={$this->siteInfo->id} and level!=0")->order('id desc')->select();
        $this->assign('info',$info[0]);
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
        $site_id = $this->siteInfo->id;
        $info  = D("Article")->where("site_id={$site_id} and valid=1")->order("sort asc")->select();
        $this->assign('id',$id);
        $this->assign('info',$info);
        $this->display();
    }
}
