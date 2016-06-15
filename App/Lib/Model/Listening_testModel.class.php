<?php
class Listening_testModel extends Model {
    
    protected $trueTableName = 'stu_listening_test';    
    
    //获取该学生测评信息
    function getInfoBySession($site_id){
        $obj = json_decode(session('levelTest')); 
        $info = D("Listening_test")->where("mobile='{$obj->mobile}' and site_id={$site_id}")->find();
        return $info;
    }
    
    //数据同步
    function aoniListeningTestData($mobile,$site_id,$is_test=null){
        $info = D("Listening_test")->where("mobile='{$mobile}' and site_id={$site_id}")->find();
        if($info['level'] !=0 && $info['schedule'] == ''){
            $sdata['student_id'] = $info['student_id'];
            $sdata['mobile'] = $mobile;
            $sdata['level'] = $info['level'];
            $sdata['is_test'] = $is_test;
            curlPost(C("STUDENT_URL")."/Api/aoniListeningTestData", $sdata);
        }
    }
   
}
