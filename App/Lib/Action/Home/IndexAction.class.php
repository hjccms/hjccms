<?php
// 本类由系统自动生成，仅供测试用途
class IndexAction extends BaseAction {
    public function index()
    {
        $cateId = $this->_get('category');
        if(!$cateId)
        {
            $this->display();
        }
        $cateInfo = D('Category')->getInfo($cateId);
      
        //if($cateInfo['site_id']!=$this->siteInfo->id)     $this->error('出错啦！');
        //print_r($cateInfo);
        $this->display('page_index_teacher');
    }
    
    function checkLevel()
    {
        if(!session('hashLevelTest') || !(session('hashLevelTest')==md5(encrypt(session('levelTest').cookie('PHPSESSID'),'E',C('APP_KEY'))))){
            redirect(U('/Index/testLogin'));
        }
        $obj = json_decode(session('levelTest'));
        $info = D("Listening_test")->getInfoBySession($this->siteInfo->id);
        $type = $this->_get('type');
        if($info){
            if($info['level'] != '0' && $type != 'again'){
                redirect(U('/Index/testResult'));
            }
            if($info['schedule'] != ''){
                $schedule = json_decode($info['schedule'],true);
                $info['end_key'] = end(array_keys($schedule));
            }
            if($type == 'again'){
                $re = D("Listening_test")->where("`name`='{$obj->name}' and mobile='{$obj->mobile}' and site_id={$this->siteInfo->id}")->save(array('schedule'=>''));
                $info['end_key'] = 0;
            }
        }else{
            $info['end_key'] = 0;
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
