<?php
/*
 * 管理菜单模型
 */
class PageModel extends Model 
{
    //获取单个菜单信息
    function getInfo($id)
    {
        if(!$id) return '';
        $data['category_id'] = $id;
        $result = $this->where($data)->find();
        return $result;
    }
    //根据ID 获取多个栏目信息
    function getAll($ids)
    {
        if(empty($ids)) return '';
        $result = $this->where("category_id in (".$ids.")")->order('sort asc')->select();
        return $result;
    }
    
}
