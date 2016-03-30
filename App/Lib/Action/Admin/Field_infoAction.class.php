<?php
/*
 * 分站管理控制器
 */
class Field_infoAction  extends BaseAction 
{
    function index()
    {
        $models = D('Model')->getModel();
        $this->assign('models',$models);
        $this->display();
    }
    
    //添加菜单
    function fieldAdd()
    {
        $modelId = $this->_get('modelId');
        //fieldtype
        $fieldType = C('FIELDTYPE');
        $this->assign('fieldType',$fieldType);
        $validformType = C('VALIDFORMTYPE');
        $this->assign('validformType',$validformType);
        $this->assign('modelId',$modelId);
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
        C('TOKEN_ON',false);
        if(!IS_POST) $this->ajaxReturn ('','非法请求！',0);
        $post = $this->_post();
        if($post['validform_type']!=''&&$post['validform_type2']!='') $post['validform_type'] .= '|'.$post['validform_type2'];
        unset($post['validform_type2']);
       
        //去模型处理其它参数
        $ret = D('Field_info')->addField($post);
        if(intval($ret)>0) $this->ajaxReturn ('','Success！',1);
        else $this->ajaxReturn ('',$ret,0);
    }
    function checkName()
    {
        $post = $this->_post();
        $ret = D('Field_info')->checkField($post['name'],$post['param']);
        
        if($ret) $ret = array('info'=>'数据重复！','status'=>'n');
        else  $ret = array('info'=>'验证成功！','status'=>'y');
        die(json_encode($ret));
    }
    function checkLongth()
    {
        $post = $this->_post();
        if(intval($post['param'])<1)  $ret = array('info'=>'请填写大于1的整数！','status'=>'n');
        else  $ret = array('info'=>'验证成功！','status'=>'y');
        die(json_encode($ret));
    }
}
