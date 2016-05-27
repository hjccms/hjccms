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
    //获取当前可用的所有菜单
    function getAllCate($cateType='',$siteId='')
    {
        $condition = " del is null ";
        if($cateType) $condition .= " and cate_type = ".$cateType;
        if($siteId) $condition .= " and site_id = ".$siteId;
        $result = $this->where($condition)->order("sort asc,id desc")->select();
        return $result;
    }
}
