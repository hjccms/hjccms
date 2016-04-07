<?php

class RoleAction extends BaseAction{
    
    function index(){
        $this->getListButton();
        $this->getContentButton();
        $this->assign('roles',D('Role')->getRole());
        $this->display();
    }
    
    function add(){
        load('@.form');
        $this->assign('menus',D('Menu')->getMenu(false,true));
        $this->display();
    }
    
    function edit(){
        load('@.form');
        $id = $this->_get('id');
        if($id>0){
            $info = D('Role')->getRoleInfo("id=".$id);
            $info["menu_ids"] = explode(",", $info["menu_ids"]);
        }
        $this->assign('info',$info);
        $this->assign('menus',D('Menu')->getMenu(false,true));
        $this->display('add');
    }
    
    function del(){
        if(!IS_AJAX) $this->ajaxReturn ('','非法请求！',0);
        $id = $this->_post('id');
        if(!$id) $this->ajaxReturn ('','非法请求！',0);
        $re = D('Role')->where("id=".$id)->delete();
        if(!$re) $this->ajaxReturn ('','操作失败！',0);
        $this->ajaxReturn ('','删除成功！',1);
    }
    
    function ajaxPost(){
        if(!IS_POST) $this->ajaxReturn ('','非法请求！',0);
        $post = $this->_post();
        $post['site_id'] = $this->adminInfo->site_id;
        $post['menu_ids'] = trim(implode(",", $post['menu_ids']),",");
        $ret = D('Role')->addRole($post);
        if(intval($ret)>0) $this->ajaxReturn ('','Success！',1);
        else $this->ajaxReturn ('',$ret,0);
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
