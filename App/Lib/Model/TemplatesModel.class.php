<?php
/*
 * 管理员模型
 */
class TemplatesModel extends Model 
{
    //获取所有可用模板
    function getAllTems($ispub='1',$con)
    {
        $condition = array('ispub'=>$ispub,'_string'=>" del is null");
        if($con) $condition['_string'] .= $con;
        $result = $this->where($condition)->select();
        return $result;
    }
    
    
    
}
