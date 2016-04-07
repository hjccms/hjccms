<?php
/*
 * 角色模型
 */
class RoleModel extends Model {
    
    //自动完成
    protected $_auto = array( array('create_time','time',1,'function') );
    
    function getRole($valid='',$site_id='',$_string=''){
        $condition = null;
        if($valid!='') $condition['valid'] = $valid;
        if($site_id!='') $condition['site_id'] = $site_id;
        if($_string!='') $condition['_string'] = $_string;
        $result = $this->where($condition)->order('id asc')->select();
        return $result;
    }
    
    //添加和更新角色
    function addRole($data)
    {
        $hash = $data['__hash__'];
        if(!$this->create($data))
        {
            $msg = $this->getError();
            return $msg;
        }
        if($data['id']>0)
        {
            
            $this->save();
            $id = $data['id'];
        }
        else
        {
            $id = $this->add();
        }
        return $id; 
       
    }  
    
    //获取单个角色详细信息
    function getRoleInfo($condition){
        $result = $this->where($condition)->find();
        return $result;
    }
}
