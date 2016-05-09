<?php

class StudentAction  extends BaseAction {

    function __construct() {
        parent::__construct();
    }
        
    function index(){
        $this->getContentButton();
        $this->getListButton();
        $sites = D('Site')->getSite("valid=1");
        $condition['site_id'] = $this->adminInfo->site_id;
        $condition['valid'] = 1;
        $condition['del'] = array('exp','is null');
        $data = D("Student")->getStudent($condition,"create_time desc");
        $this->assign('sites',$sites);
        $this->assign('data',$data);
        $this->assign('site_id',$this->adminInfo->site_id);
        $this->display();
    }
    
    function add(){
        $jw_role_type = 2;
        $cc_role_type = 3;
        $this->assign('jw',D('Admin')->getAdmin('role_type='.$jw_role_type.' and site_id='.$this->adminInfo->site_id.' and valid=1 and del is null'));
        $this->assign('cc',D('Admin')->getAdmin('role_type='.$cc_role_type.' and site_id='.$this->adminInfo->site_id.' and valid=1 and del is null'));
        load('@.form');
        $this->display();
    }
    
    function edit(){
        $id = $this->_get('id');
        if($id>0){
            $info = D('Student')->getStudentInfo($id);
        }
        $jw_role_type = 2;
        $cc_role_type = 3;
        $this->assign('jw',D('Admin')->getAdmin('role_type='.$jw_role_type.' and site_id='.$this->adminInfo->site_id.' and valid=1 and del is null'));
        $this->assign('cc',D('Admin')->getAdmin('role_type='.$cc_role_type.' and site_id='.$this->adminInfo->site_id.' and valid=1 and del is null'));
        $this->assign('info',$info);
        load('@.form');
        $this->display('add');
    }
    
    function del(){
        if(!IS_AJAX) $this->ajaxReturn ('','非法请求！',0);
        $id = $this->_post('id');
        D('Student')->where("id=".$id)->save(array("valid"=>0));
        $this->ajaxReturn ('','删除成功！',1);
    }

    //编辑学生操作
    function ajaxPost(){
        if(!IS_POST) $this->ajaxReturn ('','非法请求！',0);
        $post = $this->_post();
        if(!$post['mobile']){
            $this->ajaxReturn('','信息错误！',0);
        }else{
            //验证手机是否存在
            if($post['id']){
                $re = D("Student")->getStudentInfo("site_id={$this->adminInfo->site_id} and del is null and id!={$post['id']} and (username='{$post['mobile']}' or mobile='{$post['mobile']}')");    
            }else{
                $re = D("Student")->getStudentInfo("site_id={$this->adminInfo->site_id} and del is null and username='{$post['mobile']}' or mobile='{$post['mobile']}'");
            }
            if($re){
                $this->ajaxReturn('','手机号码已存在',0);
            }
        }
        if(!$post['password']){
            $this->ajaxReturn('','信息错误！',0);
        }
        if($post['email']){
            //验证邮箱是否存在
            if($post['id']){
                $re = D("Student")->getStudentInfo("site_id={$this->adminInfo->site_id} and del is null and id!={$post['id']} and (username='{$post['email']}' or email='{$post['email']}')");    
            }else{
                $re = D("Student")->getStudentInfo("site_id={$this->adminInfo->site_id} and del is null and username='{$post['email']}' or email='{$post['email']}'");
            }
            if($re){
                $this->ajaxReturn('','邮箱已存在',0);
            }
        }
        $post['password'] = md5($post['password']);
        $post['cc_id'] = $post['cc_id']?$post['cc_id']:$this->adminInfo->id;
        if(!$post['id']){
            $post['site_id'] = $this->adminInfo->site_id;
            $post['username'] = $post['mobile'];
            $post['create_time'] = time();
            $post['add_id'] = $this->adminInfo->id;
            $post['origin'] = '后台注册';
        }
        $ret = D('Student')->addStudent($post);
        if(intval($ret)>0){
            $post['student_id'] = $ret;
            $ret2 = D('Student')->addStudentInfo($post);  
            if(intval($ret2)>0){
                $this->ajaxReturn ('','Success！',1);
            }else{
                $this->ajaxReturn('',$ret2,0);
            }
        }else{
            $this->ajaxReturn('',$ret1,0);
        }
    }
    
}