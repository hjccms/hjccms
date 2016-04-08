<?php
/*
 * 管理员控制器
 */
class AdminAction  extends BaseAction 
{
    function index()
    {
        $this->getContentButton();
        $this->getListButton();
        //获取所有的管理员
        //获取站点和管理员的权限
        $condition = $this->getSiteCondition();
        //分页部分
        $listNum = 10;
        $page = $this->_get('page');
        $page = $page?intval($page):1; //当前页
        $this->assign('page',$page);
        $listCount =  D('Admin')->where($condition)->count();
        $startNum = $listNum*($page-1);
        $pageNum = ($listCount%$listNum==0)?($listCount/$listNum):($listCount/$listNum+1);

        $this->assign('page',$page?$page:'');
        $this->assign('pageNum',$pageNum?$pageNum:'');
        $admins = D('Admin')->where($condition)->order("id asc")->limit($startNum,$listNum)->select();
        //获取所有站点
        $sites = D('Site')->getSite();
        foreach($sites as $k=>$v)
        {
            $site[$v['id']] = $v['name'];
        }
        $this->assign('site',$site);
        $this->assign('admins',$admins);
        $this->display();
    }
    
    function adminAdd()
    {
        //调用角色
        $site_id = $this->adminInfo->site_id;
        
        $condition = array('valid'=>1,'site_id'=>$site_id);
        if($site_id>1) $condition['_string'] = " id != 1";
        $roles = D('Role')->getRole($condition);
        $this->assign('roles',$roles);
        load("@.form");
        $this->display();
    }
    function adminEdit()
    {
        load("@.form");
        //调用角色
        $site_id = $this->adminInfo->site_id;
        $id = $this->_get('id');
        $info = D('Admin')->getAdminInfo("id='$id'");
        $condition = array('valid'=>1,'site_id'=>$site_id);
        if($site_id>1) $condition['_string'] = " id != 1";
        //求出自身角色
        $selfRole =  D('Role')->getRoleInfo(array("id"=>$this->adminInfo->role_id));
        if($this->adminInfo->parent_id==0)
        {
            $roles = D('Role')->getRole($condition);
            //自身如果是总站分配的角色的话 要相应的加上
            if($selfRole['site_id']==1)
            {
                array_push($roles, $selfRole);
            }
            $roleStr = select(array('isMust'=>false,'paramArr'=>$roles,'title'=>'角色类型','inputName'=>'role_id','addHtml'=>' id="juese" errormsg="请选择"','value'=>$info['role_id']));
        
            $this->assign('roleStr',$roleStr);
        }
        
        
        
        $siteStr = '';
        $adminStr = '';
        if($info['role_type']==3&&$this->adminInfo->parent_id==0) //只有分站管理员才有权限修改本站管理员等级关系
        {
            if($this->adminInfo->site_id==1) //只有超级后台才有权限修改所属站点
            {
            //获取所有分站
                $sites = D('Site')->getSite("id>1 and valid=1");
                $siteStr = select(array('isMust'=>1,'paramArr'=>$sites,'title'=>'站点选择','inputName'=>'site_id','addHtml'=>'datatype="*" id="selsite" errormsg="请选择"','value'=>$info['site_id']));
            }
            $admins = D('Admin')->getSiteAdmins($info['site_id']);

            $allAdmins = D('Admin')->sortChilds($admins,0);
            $adminStr = select(array('isMust'=>false,'paramArr'=>$allAdmins,'title'=>'上级管理员','inputName'=>'parent_id','addHtml'=>' id="seladmin" errormsg="请选择"','value'=>$info['parent_id']));
        
            
        }
        $this->assign('siteStr',$siteStr);
        $this->assign('adminStr',$adminStr);
        $this->assign('info',$info);
        
        load("@.form");
        $this->display();
    }
    function changeRole()
    {
        load("@.form");
        $role_id = $this->_post('role_id');
        if(!$role_id||  intval($role_id)<=0) $this->ajaxReturn('','数据错误！',0);
        $roleInfo = D('Role')->getRoleInfo(array('id'=>$role_id,'valid'=>'1'));
        if($roleInfo['type']=='3'&&$this->adminInfo->site_id==1)  //权限管理员  列出分站与等级关系
        {
            //获取所有分站
            $sites = D('Site')->getSite("id>1 and valid=1");
            $siteStr = select(array('isMust'=>1,'paramArr'=>$sites,'title'=>'站点选择','inputName'=>'site_id','addHtml'=>'datatype="*" id="selsite" errormsg="请选择"','value'=>''));
            //获取所有本站管理员
            $this->ajaxReturn('',$siteStr,'1');
        }
        else 
        {
            $this->ajaxReturn('','selsite','2');
        }
        
    }
    function changeSite()
    {
        load("@.form");
        $site_id = $this->_post('site_id');
        if(!$site_id||  intval($site_id)<=0) $this->ajaxReturn('','数据错误！',0);
        $admins = D('Admin')->getSiteAdmins($site_id,'1');
        
        $allAdmins = D('Admin')->sortChilds($admins,0);
        $siteStr = select(array('isMust'=>false,'paramArr'=>$allAdmins,'title'=>'上级管理员','inputName'=>'parent_id','addHtml'=>' id="seladmin" errormsg="请选择"','value'=>''));
        //获取所有本站管理员
        $this->ajaxReturn('',$siteStr,'1');
    }
    function ajaxAdminAdd($post)
    {
        $site_id = $this->_post('site_id');
        $roleInfo = D('Role')->getRoleInfo(array('id'=>$post['role_id']));
        $post['role_type'] = $roleInfo['type'];
        if(intval($site_id)>1&&$this->adminInfo->site_id==1&&$post['role_type']==3) $post['site_id'] = $site_id;
        else  $post['site_id'] = $this->adminInfo->site_id;
        if($post['password']!=$post['password2'])   $this->ajaxReturn('','密码不一致！',0);
        $post['password'] = md5(encrypt($post['password'], 'E'));
        return $post;
    }
    function ajaxAdminEdit($post)
    {
        $site_id = $this->_post('site_id');
        $roleInfo = D('Role')->getRoleInfo(array('id'=>$post['role_id']));
        $post['role_type'] = $roleInfo['type'];
        if(intval($site_id)>1&&$this->adminInfo->site_id==1&&$post['role_type']==3) $post['site_id'] = $site_id;
        else  $post['site_id'] = $this->adminInfo->site_id;
        //重新验证密码
        if($this->_post('oldpassword'))
        {
            $oldPassword = md5(encrypt($this->_post('oldpassword'),'E'));
            $check = D('Admin')->getAdminInfo("id='$post[id]' and password='$oldPassword'");
            if(!$check)  $this->ajaxReturn('','旧密码错误！',0);
            if($post['password']!=$post['password2'])   $this->ajaxReturn('','密码不一致！',0);
        }
        $post['password'] = md5(encrypt($post['password'], 'E'));
        return $post;
    }
    //检查旧密码是否正确
    function checkOldPass()
    {
        $adminId = $this->_get('id');
        $password = md5(encrypt($this->_post('param'),'E'));
        $info = D('Admin')->getAdminInfo("id='$adminId' and password='$password'");
        if($info) $this->ajaxReturn('','验证成功！','y');
        else  $this->ajaxReturn('','密码错误！','n');
    }
}
