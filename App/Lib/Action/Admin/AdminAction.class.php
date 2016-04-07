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
        //调用角色
        $site_id = $this->adminInfo->site_id;
        
        $condition = array('valid'=>1,'site_id'=>$site_id);
        if($site_id>1) $condition['_string'] = " id != 1";
        $roles = D('Role')->getRole($condition);
        $this->assign('roles',$roles);
        load("@.form");
        $this->display();
    }
    function changeRole()
    {
        load("@.form");
        $role_id = $this->_post('role_id');
        if(!$role_id||  intval($role_id)<=0) $this->ajaxReturn('','数据错误！',0);
        $roleInfo = D('Role')->getRoleInfo(array('id'=>$role_id,'valid'=>'1'));
        if($roleInfo['type']=='3'&&$this->adminInfo->site_id==1)  //权限管理员  列出分站与等级关系
        {
            //获取所有分站
            $sites = D('Site')->getSite("id>1 and valid=1");
            $siteStr = select(array('isMust'=>1,'paramArr'=>$sites,'title'=>'站点选择','inputName'=>'site_id','addHtml'=>'datatype="*" id="selsite" errormsg="请选择"','value'=>''));
            //获取所有本站管理员
            $this->ajaxReturn('',$siteStr,'1');
        }
        else 
        {
            $this->ajaxReturn('','selsite','2');
        }
        
    }
    function changeSite()
    {
        load("@.form");
        $site_id = $this->_post('site_id');
        if(!$site_id||  intval($site_id)<=0) $this->ajaxReturn('','数据错误！',0);
        $admins = D('Admin')->getSiteAdmins($site_id,'1');
        
        $allAdmins = D('Admin')->sortChilds($admins,0);
        $siteStr = select(array('isMust'=>false,'paramArr'=>$allAdmins,'title'=>'上级管理员','inputName'=>'parent_id','addHtml'=>' id="seladmin" errormsg="请选择"','value'=>''));
        //获取所有本站管理员
        $this->ajaxReturn('',$siteStr,'1');
    }
    function ajaxAdminAdd($post)
    {
        print_r($post);
        die();
    }
}
