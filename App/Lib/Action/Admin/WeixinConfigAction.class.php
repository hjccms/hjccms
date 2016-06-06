<?php
class WeixinConfigAction extends WeixinBaseAction{
    
    function index(){
        $this->getContentButton();
        $this->getListButton();
        load('@.form');
        
        $site_id = $this->adminInfo->site_id;
        $info = D("Weixin_config")->getInfo("site_id={$site_id}");
        if(!$info){
            $data['site_id'] = $site_id;
            $data['admin_id'] = $this->adminInfo->id;
            $data['create_time'] = time();
            $data['token'] = get_rand_char(8);
            D("Weixin_config")->add($data);
            $info = D("Weixin_config")->getInfo("site_id={$site_id}");
        }
        if($info){
            $info['url'] = str_replace('{token}', $info['token'], C("WEIXIN_URL"));
        }
        $this->assign("info",$info);
        $this->display();
    }
    
    
    //编辑菜单操作
    function ajaxPost(){
        if(!IS_POST) $this->ajaxReturn ('','非法请求！',0);
        $post = $this->_post();
        if(!$post['id']){
            $this->ajaxReturn ('','操作失败',0);
        }
        $ret = D("Weixin_config")->where("id={$post['id']}")->save($post);
        if(intval($ret)>0) $this->ajaxReturn ('','Success！',1);
        else $this->ajaxReturn ('','操作失败',0);
    }
    
}
