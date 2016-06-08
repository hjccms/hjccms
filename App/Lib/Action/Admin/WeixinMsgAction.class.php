<?php
//微信消息
class WeixinMsgAction extends WeixinBaseAction{
    
    function index(){
        $this->getContentButton();
        $this->getListButton();
        load('@.form');
        load('@.weixin');
        $data = D('Weixin_msg')->getMsg("site_id={$this->adminInfo->site_id} and del is null");
        $this->assign('data',$data);
        $this->display();
    }
    
    function add(){
        load('@.form');
        load('@.weixin');
        $this->display();
    }
    
    function edit(){
        $id = $this->_get('id');
        if($id>0){
            $info = D('Weixin_msg')->getInfo($id);
        }
        $this->assign('info',$info);
        load('@.form');
        load('@.weixin');
        $this->display('add');
    }
    
    //删除
    function del(){
        if(!IS_AJAX) $this->ajaxReturn ('','非法请求！',0);
        $id = $this->_post('id');
        D('Weixin_msg')->where("id=".$id)->save(array("del"=>1));
        $this->ajaxReturn ('','删除成功！',1);
    }

    //编辑菜单操作
    function ajaxPost(){
        if(!IS_POST) $this->ajaxReturn ('','非法请求！',0);
        $post = $this->_post();
        if(!$post['id']){
            $post['site_id'] = $this->adminInfo->site_id;
            $post['admin_id'] = $this->adminInfo->id;
            $post['create_time'] = time();
        }        
        $ret = D('Weixin_msg')->addMsg($post);
        if(intval($ret)>0) $this->ajaxReturn ('','Success！',1);
        else $this->ajaxReturn ('',$ret,0);
    }
}
