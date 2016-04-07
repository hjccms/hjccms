<?php

class RoleAction extends BaseAction{
    
    function index(){
        load('@.form');
        $this->getListButton();
        $this->getContentButton();
        $site_id = ($this->_get('site_id') !== null)?$this->_get('site_id'):$this->adminInfo->site_id;
        $condition['valid'] = 1;
        if($site_id>0){ 
            $condition['site_id'] = $site_id;
        }
        $this->assign('sites',$this->getSite());
        $this->assign('roles',D('Role')->getRole($condition));
        $this->assign('data',$condition);
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
        if($id>1){
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
        if($id<=1) $this->ajaxReturn ('','非法请求！',0);
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
    
    function getSite(){
        $siteArr = null;
        $sites = D("Site")->getSite("valid=1");
        foreach ($sites as $k=>$v){
            $siteArr[$k+1] = $v;
        }
        $siteArr[0] = array('id'=>0,'name'=>'全部');
        sort($siteArr);
        return $siteArr;
    }
     
}
