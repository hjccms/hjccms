<?php
/*
 * 分站管理控制器
 */
class SiteAction  extends BaseAction 
{
    function index()
    {
        $this->getContentButton();
        $this->getListButton();
        $sites = D('Site')->getSite();
        $this->assign('sites',$sites);
        $this->display();
    }
    
    //添加菜单
    function siteAdd()
    {
        
        load('@.form');
        $this->display();
    }
    //添加菜单
    function siteEdit()
    {
        $id = $this->_get('id');
        $info = D('Site')->getSite($id);
        $this->assign('info',$info);
        $this->assign('id', $id);
      
        load('@.form');
        $this->display();
    }
    function ajaxPost()
    {
        if(!IS_POST) $this->ajaxReturn ('','非法请求！',0);
        $post = $this->_post();
        
        $post['admin_id'] = $this->adminInfo->id;
        //去模型处理其它参数
        $ret = D('Site')->addSite($post);
        if(intval($ret)>0) $this->ajaxReturn ('','Success！',1);
        else $this->ajaxReturn ('',$ret,0);
    }
}
