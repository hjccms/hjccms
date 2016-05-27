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
        $info = D("Listening_test")->getInfoBySession($this->siteInfo->id);
        if($info){
            if($info['level'] != '0'){
                redirect(U('/Index/testResult'));
            }
            if($info['level'] == '0' && $info['schedule'] != ''){
                $schedule = json_decode($info['schedule'],true);
                $info['end_key'] = end(array_keys($schedule));
                $info['end_value'] = end(array_values($schedule));
            }
        }else{
            $info['end_key'] = 0;
            $info['end_value'] = 0;
        }
        $this->assign('info',$info);
        $this->display();
    }
    function testLogin()
    {
        if(session('hashLevelTest') && (session('hashLevelTest')==md5(encrypt(session('levelTest').cookie('PHPSESSID'),'E',C('APP_KEY'))))){
            redirect(U('/Index/checkLevel'));
        }
        $this->display();
    }
    function testResult()
    {
        $obj = json_decode(session('levelTest'));
        $info = D("Listening_test")->getInfoBySession($this->siteInfo->id);
        if($info['level'] == 0){
            redirect(U('/Index/checkLevel'));
        }
        $info['level_text'] = get_listening_level_text($info['level']);
        $this->assign('info',$info);
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
