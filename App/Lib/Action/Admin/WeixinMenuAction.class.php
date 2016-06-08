<?php
//微信菜单
class WeixinMenuAction extends WeixinBaseAction{
    
    function index(){
        $menu = D('Weixin_menu')->getMenu("site_id={$this->adminInfo->site_id} and valid=1 and del is null");
        $menuTree = D('Weixin_menu')->getListTree($menu,$this->menuId);
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
        $this->assign('menus',$this->getInfo());
        load('@.form');
        $this->display();
    }
    
    function edit(){
        $id = $this->_get('id');
        if($id>0){
            $info = D('Weixin_menu')->getInfo($id);
        }
        $this->assign('menus',$this->getInfo());
        $this->assign('info',$info);
        load('@.form');
        $this->display('add');
    }
    
    function del(){
        if(!IS_AJAX) $this->ajaxReturn ('','非法请求！',0);
        $id = $this->_post('id');
        //检查是否有子菜单，如果有子菜单，需要先删除子菜单
        $result = D('Weixin_menu')->getMenu("site_id={$this->adminInfo->site_id} and valid=1 and del is null",$id,true);
        if($result)  $this->ajaxReturn ('','请先删除子菜单！',0);
        D('Weixin_menu')->where("id=".$id)->delete();
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
        if(!$post['url']){
            $post['key'] = get_rand_char(6);
        }
        $post['level'] = D('Weixin_menu')->getLevel($post['parent_id']);
        $ret = D('Weixin_menu')->addMenu($post);
        if(intval($ret)>0) $this->ajaxReturn ('','Success！',1);
        else $this->ajaxReturn ('',$ret,0);
    }
    
    function getInfo(){
        //获取菜单列表
        $menuArr = null;
        $menus = D('Weixin_menu')->getMenu("site_id={$this->adminInfo->site_id} and valid=1 and del is null");
        foreach ($menus as $k=>$v){
            $menuArr[$k+1] = $v;
        }
        $menuArr[0] = array('id'=>0,'name'=>'顶级菜单');
        sort($menuArr);
        return $menuArr;
    }
    
    function createMenu(){
        $site_id = $this->adminInfo->site_id;
        $access_token = $this->getAccessToken();
        if(!$site_id || !$access_token){
            $this->ajaxReturn ('','操作失败！1',0);
        }
        $menu = D('Weixin_menu')->getMenu("site_id={$this->adminInfo->site_id} and level<=1 and valid=1 and del is null");
        if(!$menu){
            $this->ajaxReturn ('','操作失败！2',0);
        }
        $str = '{"button":[';
        foreach($menu as $mk=>$mv){
            if($mv['childs']){
                $str .= '{"name":"'.$mv['name'].'","sub_button":[';
                foreach($mv['childs'] as $ck=>$cv){
                    if($cv['url']){
                        $str .= '{"type":"view","name":"'.$cv['name'].'","url":"'.$cv['url'].'"},';
                    }else{
                        $str .= '{"type":"click","name":"'.$cv['name'].'","key":"'.$cv['key'].'"},';
                    }
                }
                $str .= ']},';
            }else{
                if($mv['url']){
                    $str .= '{"type":"view","name":"'.$mv['name'].'","url":"'.$mv['url'].'"},';
                }else{
                    $str .= '{"type":"click","name":"'.$mv['name'].'","key":"'.$mv['key'].'"},';
                }
                
            }
        }
        $str .= ']}';
        file_get_contents('https://api.weixin.qq.com/cgi-bin/menu/delete?access_token='.$access_token);
        $url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$access_token;
        $re = json_decode(actionPost($url, $str));
        if($re->errcode == 0){
            $this->ajaxReturn ('','操作成功！',1);
        }else{
            $this->ajaxReturn ('','操作失败！',0);
        }
        
    }
    
}
