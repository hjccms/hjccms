<?php

class RoleOldAction extends BaseAction{
    
    function index(){
        $role = D('Role')->getRole();
        $roleTree = D('Role')->getListTree($role,$this->menuId);
        $this->getContentButton();
        $this->assign('roleTree',$roleTree);
        $this->display();
    }
    
    function add(){
        load('@.form');
        $this->assign('menus',D('Menu')->getMenu(false,true));
        $this->assign('roles',$this->getRole());
        $this->assign('sites',D("Role")->getSites());
        $this->display();
    }
    
    function edit(){
        load('@.form');
        $id = $this->_get('id');
        if($id>0){
            $info = D('Role')->getInfo($id);
            $info["site_ids"] = explode(",", $info["site_ids"]);
            $info["menu_ids"] = explode(",", $info["menu_ids"]);
        }
        $this->assign('info',$info);
        $this->assign('menus',D('Menu')->getMenu(false,true));
        $this->assign('sites',D("Role")->getSites());
        $this->assign('roles',$this->getRole());
        $this->display('add');
    }
    
    function del(){
        if(!IS_AJAX) $this->ajaxReturn ('','非法请求！',0);
        $id = $this->_post('id');
        //检查是否有子角色，如果有子角色，需要先删除子角色
        $result = D('Role')->getRole($id,true);
        if($result)  $this->ajaxReturn ('','请先删除子角色！',0);
        D('Role')->where("id=".$id)->delete();
        $this->ajaxReturn ('','删除成功！',1);
    }
    
    function ajaxPost(){
        if(!IS_POST) $this->ajaxReturn ('','非法请求！',0);
        $post = $this->_post();
        $post['site_ids'] = trim(implode(",", $post['site_ids']),",");
        $post['menu_ids'] = trim(implode(",", $post['menu_ids']),",");
        $ret = D('Role')->addRole($post);
        if(intval($ret)>0) $this->ajaxReturn ('','Success！',1);
        else $this->ajaxReturn ('',$ret,0);
    }
    
    function getRole(){
        $roleArr = null;
        $roles = D('Role')->getRole();
        foreach ($roles as $k=>$v){
            $roleArr[$k+1] = $v;
        }
        $roleArr[0] = array('id'=>0,'name'=>'顶级管理员');
        sort($roleArr);
        return $roleArr;
    }
    
    function ajaxGetInfo(){
        load('@.form');
        if(!IS_POST) $this->ajaxReturn ('','非法请求！',0);
        $post = $this->_post();
        $id = $post['id'];
        $roleInfo = D("Role")->getRoleInfo("id=".$id);
        $menus = D('Menu')->getMenu(false,true,true,false,$roleInfo['menu_ids']);
        $sites = D("Role")->getSites($roleInfo['site_ids']);
        $siteStr = checkbox(array('title'=>'分校','inputName'=>'site_ids[]','value'=>explode(",", $roleInfo["site_ids"]),'tipMsg'=>'','height'=>'120px','addClass_2'=>'hid','paramArr'=>$sites));
        $menuStr = checkbox(array('title'=>'权限','inputName'=>'menu_ids[]','value'=>explode(",", $roleInfo["menu_ids"]),'addClass'=>'ckbox','addClass_2'=>'hid','tipMsg'=>'','height'=>'400px','paramArr'=>$menus));
        $reStr = '<script type="text/javascript" src="/Public/Style/Admin/js/role.js"></script>'.$siteStr.$menuStr;
        if($reStr) $this->ajaxReturn ($reStr,'Success！',1);
        else $this->ajaxReturn ('','错误',0);
    }
    
    
}
