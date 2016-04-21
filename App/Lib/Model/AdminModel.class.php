<?php
/*
 * 管理员模型
 */
class AdminModel extends Model 
{
    //获取单个管理员详细信息
    function getAdminInfo($condition)
    {
        $result = $this->where($condition)->find();
        return $result;
    }
    //更新管理员信息 以主键为准
    function updateAdminInfo($data)
    {
        $this->data($data)->save();
    }
    //获取某一站点所有可用的管理员
    function getSiteAdmins($site_id='',$valid='',$_string='',$parentId=0)
    {
        if($site_id!='') $condition['site_id'] = $site_id;
        if($valid!='') $condition['valid'] = $valid;
        $condition['_string'] = " del is null ";
        if($_string!='') $condition['_string'] .= ' and '.$_string;
        
        $admins = $this->where($condition)->select();
        if(empty($admins)) return '';
     
        
        $admins = $this->sortChilds($admins, $parentId);
        
        return $admins;
    }
    
    function sortChilds($dataArr,$parentId)
    {
        if(!is_array($dataArr)||empty($dataArr)) return '';
        foreach ($dataArr as $k=>$v)
        {
            $allParents[$k] = $v['parent_id'];
        }
        
        if(!in_array($parentId,$allParents)) return ''; 
        foreach ($dataArr as $k=>$v)
        {
            if($v['parent_id']==$parentId)
            {
                $result[$k] = $v;
                $result[$k]['name'] = $v['username'];
                $result[$k]['childs'] = $this->sortChilds($dataArr , $v['id']);
            }
        }
        return $result;
    }
    
    function getSiteCondition($site_id,$adminId,$valid='1')
    {
        if($site_id>1)  //如果不是不是超级站点的话  取当前站点 当前管理员 以及当前管理员下面的数据
        {
            $condition['site_id'] = $site_id;
            $admins = $this->getSiteAdmins($site_id,$valid,'',$adminId);
            
            
            $adminStr = $adminId.$this->sortAdminIds($admins);
            $condition['admin_id'] = array('in',$adminStr);
        }
        return $condition;
    }
    //递归组合ID
    function sortAdminIds($admins)
    {
        if(!$admins) return '';
      
        foreach($admins as $k=>$v)
        {
            $adminStr .= ','.$v['id'];
            if(!empty($v['childs']))
            {
                $adminStr .= $this->sortAdminIds($v['childs']);
              
            }
        }
        return $adminStr;
    }
    
}
