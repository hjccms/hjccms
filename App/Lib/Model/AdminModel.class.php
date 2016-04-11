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
    function getSiteAdmins($site_id='',$valid='',$_string='')
    {
        if($site_id!='') $condition['site_id'] = $site_id;
        if($valid!='') $condition['valid'] = $valid;
        $condition['_string'] = " del is null ";
        if($_string!='') $condition['_string'] .= ' and '.$_string;
        
        $admins = $this->where($condition)->select();
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
}
