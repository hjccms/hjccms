<?php
// 本类由系统自动生成，仅供测试用途
class IndexAction extends BaseAction {
    public function index()
    {
        $cateId = $this->_get('category');
        if(!$cateId)
        {
            $this->display('index');
            die();
        }
        $cateInfo = D('Category')->getInfo($cateId);
        //如果导航和脚步导航一个的话直接指向脚步导航
        foreach($this->footer_nav as $k=>$v){
            if($cateInfo['name'] == $v['name']){
                $cateId = $v['id'];
                $cateInfo = D('Category')->getInfo($v['id']);
            }
        }
        if($cateInfo['site_id']!=$this->siteInfo->id)     $this->error('出错啦！');
        $this->assign('cateInfo',$cateInfo);
        $modelInfo = D('Model')->getModel($cateInfo['mid']);
        
        //如果是外部链接 直接跳转走
        if($cateInfo['outlink']==1) redirect($cateInfo['linkurl']);
        //获取当前栏目最顶级栏目ID
        $topId = D('Category')->getTopId($cateId);
        $childsCate  = D('Category')->getAllCate('',$this->siteInfo->id,$topId);
        
        $this->assign("childsCate",$childsCate);
        $this->assign("topId",$topId);
        $this->assign("cateid",$cateId);
        
        if(method_exists($this,$modelInfo['table_name'])) $this->$modelInfo['table_name']($cateInfo,$childsCate);        
        $cateInfo['template_index'] = str_replace(".html",'',$cateInfo['template_index']);
        $this->display($cateInfo['template_index']);
    }
    
    //单页处理
    function page($cateInfo,$childsCate)
    {
        //获取单页的所有信息
        $info = D('Page')->getInfo($cateInfo['id']);
        $this->assign('info',$info);
        //获取所有子栏目信息
        foreach($childsCate as $k=>$v)
        {
            $ids .= ','.$v['id'];
        }
        $ids = substr($ids, 1);
        if(!empty($ids))
        {
            $childsCateInfo = D('Page')->getAll($ids);
        }
        foreach($childsCateInfo as $k=>$v){
            $childsCateInfo[$k]['content2'] = str_replace("'","--dh--",$v['content']);
        }
        $this->assign('childsCateInfo',$childsCateInfo);
    }
    function checkLevel()
    {
        if($this->userInfo->id){ 
            session('levelTest',  json_encode(array('name'=>$this->userInfo->username,'mobile'=>$this->userInfo->username)));
            session('hashLevelTest',md5(encrypt(json_encode(array('name'=>$this->userInfo->username,'mobile'=>$this->userInfo->username)).cookie('PHPSESSID'),'E',C('APP_KEY'))));
        }
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
                $re = D("Listening_test")->where("mobile='{$obj->mobile}' and site_id={$this->siteInfo->id}")->save(array('schedule'=>''));
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
        if($this->userInfo->id){ 
            session('levelTest',  json_encode(array('name'=>$this->userInfo->username,'mobile'=>$this->userInfo->username)));
            session('hashLevelTest',md5(encrypt(json_encode(array('name'=>$this->userInfo->username,'mobile'=>$this->userInfo->username)).cookie('PHPSESSID'),'E',C('APP_KEY'))));
        }
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
        
        //数据同步
        D("Listening_test")->aoniListeningTestData($obj->mobile,$this->siteInfo->id);
        
        $this->assign('info',$info);
        $this->display();
    }
    
    function course()
    {
        C('SHOW_PAGE_TRACE',false);
        $this->display();
    }
}
