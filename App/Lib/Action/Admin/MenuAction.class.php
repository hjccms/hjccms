<?php


class MenuAction extends BaseAction 
{
    function index()
    {
        $menu = D('Menu')->getMenu();
        $menuTree = D('Menu')->getListTree($menu,$this->menuId);
        $this->getContentButton();
        $this->assign('menuTree',$menuTree);
        $this->display();
    }
    //编辑菜单页面显示
    function menuAdd()
    {
        $id = $this->_get('id');
        $menus = D('Menu')->getMenu();
        $menuArr = array();
        foreach ($menus as $k=>$v){
            $menuArr[$k+1] = $v;
        }
        //获取菜单信息
        if($id>0) $info = D('Menu')->getInfo($id);
        else $info = array();
        
        //菜单列表中顶级菜单在最上面
        if(!$menuArr||$info['parent_id']==0)
        {
            $menuArr[0] = array('id'=>0,'name'=>'顶级菜单');
        }
        
        //排序菜单列表
        sort($menuArr);
        
        $this->assign('info',$info);
        $this->assign('menus',$menuArr);
        load('@.form');
        $this->display();
    }
    
    //编辑菜单操作
    function ajaxPost()
    {
        if(!IS_POST) $this->ajaxReturn ('','非法请求！',0);
        $post = $this->_post();
        $post['admin_id'] = $this->adminInfo->id;
        $post['level'] = D('Menu')->getLevel($post['parent_id']);
        //去模型处理其它参数
        $ret = D('Menu')->addMenu($post);
        if(intval($ret)>0) $this->ajaxReturn ('','Success！',1);
        else $this->ajaxReturn ('',$ret,0);
    }
    
    //删除菜单
    function delMenu()
    {
        if(!IS_AJAX) $this->ajaxReturn ('','非法请求！',0);
        $id = $this->_post('id');
        //检查是否有子菜单，如果有子菜单，需要先删除子菜单
        $result = D('Menu')->getMenu($id,true);
        if($result)  $this->ajaxReturn ('','请先删除子菜单！',0);
        D('Menu')->where("id=".$id)->delete();
        $this->ajaxReturn ('','删除成功！',1);
    }
}
