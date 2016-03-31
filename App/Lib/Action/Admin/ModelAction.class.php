<?php
/*
 * 分站管理控制器
 */
class ModelAction  extends BaseAction 
{
    function index()
    {
        $this->getContentButton();
        $this->getListButton();
        $models = D('Model')->getModel();
        $this->assign('models',$models);
        $this->display();
    }
    
    //添加菜单
    function modelAdd()
    {
        $id = $this->_get('id');

        load('@.form');
        $this->display();
    }
    //修改功能
    function modelEdit()
    {
        $id = $this->_get('id');
      
        //info信息
        if($id>0) $info = D('Model')->getModel($id);
        else $info = array();
        $this->assign('info',$info);
        $this->assign('id', $id);
        load('@.form');
        $this->display();
    }
    function ajaxPost()
    {
        if(!IS_POST) $this->ajaxReturn ('','非法请求！',0);
        $post = $this->_post();
        
        //去模型处理其它参数
        $ret = D('Model')->addModel($post);
        if(intval($ret)>0) $this->ajaxReturn ('','Success！',1);
        else $this->ajaxReturn ('',$ret,0);
    }
    function checkName()
    {
        $post = $this->_post();
        $id = $this->_get('id');
        if(intval($id)>0) $condition['_string'] = "id!='$id'";
        $ret = D('Model')->checkField($post['name'],$post['param'],$condition);
        
        if($ret) $ret = array('info'=>'数据重复！','status'=>'n');
        else  $ret = array('info'=>'验证成功！','status'=>'y');
        die(json_encode($ret));
    }
}
