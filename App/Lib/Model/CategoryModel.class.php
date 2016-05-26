<?php
/*
 * 管理菜单模型
 */
class CategoryModel extends Model 
{
    //获取单个菜单信息
    function getInfo($id)
    {
        if(!$id) return '';
        $data['id'] = $id;
        $result = $this->where($data)->find();
        return $result;
    }
    
}
