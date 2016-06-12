<?php
class Listening_testModel extends Model {
    
    protected $trueTableName = 'stu_listening_test';    
    
    //获取该学生测评信息
    function getInfoBySession($site_id){
        $obj = json_decode(session('levelTest')); 
        $info = D("Listening_test")->where("mobile='{$obj->mobile}' and site_id={$site_id}")->find();
        return $info;
    }
   
}
