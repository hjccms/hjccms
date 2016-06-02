<?php
class MenuAction extends BaseAction {
    
    function index(){
        $menu = D('Menu')->getMenu();
        $menuTree = D('Menu')->getListTree($menu,$this->menuId);
        $this->getContentButton();
        $this->assign('menuTree',$menuTree);
        $this->display();
    }
    
    function add(){
        if($parent_id = $this->_get('parent_id'))
        {
            $info['parent_id'] = $parent_id;
        }
        $this->assign('info',$info);
        $this->assign('menus',$this->getMenu());
        load('@.form');
        $this->display();
    }
    
    function edit(){
        $id = $this->_get('id');
        if($id>0){
            $info = D('Menu')->getInfo($id);
        }
        $this->assign('menus',$this->getMenu());
        $this->assign('info',$info);
        load('@.form');
        $this->display('add');
    }
    
    function del(){
        if(!IS_AJAX) $this->ajaxReturn ('','非法请求！',0);
        $id = $this->_post('id');
        //检查是否有子菜单，如果有子菜单，需要先删除子菜单
        $result = D('Menu')->getMenu($id,true);
        if($result)  $this->ajaxReturn ('','请先删除子菜单！',0);
        D('Menu')->where("id=".$id)->delete();
        $this->ajaxReturn ('','删除成功！',1);
    }

    //编辑菜单操作
    function ajaxPost(){
        if(!IS_POST) $this->ajaxReturn ('','非法请求！',0);
        $post = $this->_post();
        $post['admin_id'] = $this->adminInfo->id;
        $post['level'] = D('Menu')->getLevel($post['parent_id']);
        $ret = D('Menu')->addMenu($post);
        if(intval($ret)>0) $this->ajaxReturn ('','Success！',1);
        else $this->ajaxReturn ('',$ret,0);
    }
    
    function getMenu(){
        //获取菜单列表
        $menuArr = null;
        $menus = D('Menu')->getMenu();
        foreach ($menus as $k=>$v){
            $menuArr[$k+1] = $v;
        }
        $menuArr[0] = array('id'=>0,'name'=>'顶级菜单');
        sort($menuArr);
        return $menuArr;
    }
}
