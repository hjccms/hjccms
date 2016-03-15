<?php
/*
 * 管理菜单模型
 */
class MenuModel extends Model 
{
    //自动完成
    protected $_auto = array( array('create_time','time',1,'function') );
    //获取自级菜单 $status 为真 只查询 正常的菜单  否则全部查询  $child 为真 查询子菜单 否则不查询
    function getMenu($parentId='',$valid='',$child=true)
    {
        $condition = array();
        
        if($parentId!='') $condition['parent_id'] = $parentId;
        if($valid!='') $condition['valid'] = $valid;
        $result = $this->where($condition)->order('sort asc,id asc')->select();
        //处理子集数据
        
        $ret = $this->sortChilds($result,0);
        
        return $ret;
    }
    //排列子集
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
                $result[$k]['childs'] = $this->sortChilds($dataArr , $v['id']);
            }
        }
        return $result;
    }
    //获取单个菜单信息
    function getInfo($id)
    {
        if(!$id) return '';
        $data['id'] = $id;
        $result = $this->where($data)->find();
        return $result;
    }
    //添加和更新菜单
    function addMenu($data)
    {
        if($data['id']>0)
        {
            $this->data($data)->save();
            $id = $data['id'];
        }
        else
        {
            $this->create($data);
            $id = $this->add();
        }
        return $id;
    }
}
