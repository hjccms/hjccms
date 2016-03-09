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
}
