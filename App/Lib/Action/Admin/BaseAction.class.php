<?php
/*
 * 后台控制器基础类
 */
class BaseAction  extends Action 
{
    var $adminInfo;
    var $adminSiteInfo;
    var $menuId;
    function __construct() 
    {
        parent::__construct();
        //验证登陆状态
        if(session('hashuser')&&session('hashuser')==$this->hashUser())
        {
            //设置管理员信息
            if(cookie('adminInfo'))
            {
                $this->adminInfo = cookie('adminInfo');
            }
            else
            {
                $adminInfo = D('Admin')->getAdminInfo('id='.session('adminId'));
                if(isset($adminInfo['role_id'])){
                    $roleInfo = D("Role")->getRoleInfo("id=".$adminInfo['role_id']);
                    $adminInfo['menu_ids'] = encrypt($roleInfo['menu_ids'],'E',C('APP_KEY'));
                }
                cookie('adminInfo',$adminInfo,3600*24*7);//保存时间足够一次不间断的操作
                $this->adminInfo = cookie('adminInfo');
            }
            
            if(cookie('adminSiteInfo'))
            {
                $this->adminSiteInfo = cookie('adminSiteInfo');
            }
            else
            {
                $adminSiteInfo = D('Site')->getInfo($this->adminInfo->site_id);
                $weixinConfig = D("Weixin_config")->getInfo("site_id={$this->adminInfo->site_id}");
                $adminSiteInfo['appid'] = encrypt($weixinConfig['appid'],'E',C('APP_KEY'));
                $adminSiteInfo['appsecret'] = encrypt($weixinConfig['appsecret'],'E',C('APP_KEY'));
                cookie('adminSiteInfo',$adminSiteInfo,3600*24*7);//保存时间足够一次不间断的操作
                $this->adminSiteInfo = cookie('adminSiteInfo');
            }
            if($this->adminInfo->site_id=='1')
            {
                $allSites = D('Site')->getSite();
                $this->assign('allSites',$allSites);
            }
            $this->assign('adminInfo',$this->adminInfo);
            $this->menuId = $this->getMenuId();
            $this->getPosition();
            if(!$this->menuId){
                return true;
            }
            //查看是否有权限访问该站点
            if($this->adminInfo->role_type > 2){
                $isCheck = D("Site")->checkSite($this->adminInfo->site_id);
                if(!$isCheck){
                    if(IS_AJAX) $this->ajaxReturn('','没有权限访问该站点!',0);
                    else $this->error("没有权限访问该站点！");die;
                }
            }
            //查看是否有权限访问该模块
            if($this->adminInfo->role_type != 1 && MODULE_NAME != 'Index'){
                $isCheck = D("Menu")->checkMenu($this->adminInfo->role_id, $this->menuId, explode(",", encrypt($this->adminInfo->menu_ids,'D')));
                if(!$isCheck){
                    if(IS_AJAX) $this->ajaxReturn('','没有权限访问该模块!',0);
                    else $this->error("没有权限访问该模块！");die;
                }
            }
            if(!empty($_GET['fromUrl'])) $this->assign('fromUrl',$_GET['fromUrl']);
        }
        else
        {
            //去登陆
            redirect(U('/Admin/Login/index'));
        }
    }
    function hashUser()
    {
        return md5(encrypt(session('username').cookie('PHPSESSID'),'E',C('APP_KEY').session('adminId')));
    }
    
    //获取位置
    function getPosition(){
        if(!$this->menuId) return false;
        $position = D("Menu")->getPositionById($this->menuId);
        $this->assign('position',$position);
    }
    
    //获取内容按钮
    function getContentButton(){
        if(!$this->menuId) return false;
        $contentButton = D('Menu')->getMenu($this->menuId,true,false,2);
        $this->assign('contentButton',$contentButton);
    }
    
    //获取列表按钮
    function getListButton(){
        if(!$this->menuId) return false;
        $listButton = D('Menu')->getMenu($this->menuId,true,false,3);
        $this->assign('listButton',$listButton);
    }
    
    //获取菜单id
    function getMenuId(){
        $id = D('Menu')->getIdByUrl(str_replace("Action", "", MODULE_NAME),ACTION_NAME,get_param());
        return $id;
    }
    
    
    //判断站点和管理员的条件
    function getSiteCondition()
    {
        $condition = array();
        $site_id = $this->adminInfo->site_id;
        $adminId = $this->adminInfo->id;
        $condition = D('Admin')->getSiteCondition($site_id,$adminId);
        
        return $condition;
    }
    
}

