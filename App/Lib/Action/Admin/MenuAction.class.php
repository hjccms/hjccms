<?php


class MenuAction extends BaseAction 
{
    function index()
    {
        $result = D('Menu')->getMenu();
        $this->assign('result',$result);
        $this->display();
    }
    //添加菜单
    function menuAdd()
    {
        $id = $this->_get('id');
        if(!$id)
        {
            $id=0;
            $menus = array(array('id'=>'0','name'=>'顶级菜单'));
        }
        else
        {
            $menus = D('Menu')->getMenu();
        }
        //info信息
        if($id>0) $info = D('Menu')->getInfo($id);
        else $info = array();
        $this->assign('info',$info);
        $this->assign('id', $id);
        $this->assign('parentId', $info['parentId']);
        $this->assign('menus',$menus);
        load('@.form');
        $this->display();
    }
    function ajaxPost()
    {
        if(!IS_POST) $this->ajaxReturn ('','非法请求！',0);
        $post = $this->_post();
        //把值为NULL的改为空
        foreach($post as $k=>$v)
        {
            if($v=='NULL') $post[$k] = '';
        }
        $post['site'] = $this->adminInfo['site'];
        $post['adminId'] = $this->adminInfo['id'];
        //去模型处理其它参数
        $id = D('Menu')->addMenu($post);
        if($id) $this->ajaxReturn ('','Success！',1);
        else $this->ajaxReturn ('','error！',0);
    }
    //删除菜单
    function delMenu()
    {
        if(!IS_AJAX) $this->ajaxReturn ('','非法请求！',0);
        $id = $this->_post('id');
        //检查是否有子菜单
        $result = D('Menu')->getMenu($id,false,false);
        if($result)  $this->ajaxReturn ('','请先删除子菜单！',0);
        //如果不存在 删除本菜单
        D('Menu')->where("id=".$id)->delete();
        $this->ajaxReturn ('','删除成功！',1);
    }
}
