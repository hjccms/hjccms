<?php
class Student_infoModel extends Model {
    
    protected $trueTableName = 'stu_student_info';    
    
    //添加和更新学生详细信息
    function addStudentInfo($data){
        if($data['id']){
            $this->save($data);
            $id = $data['student_id'];
        }else{
            $id = $this->add($data);
        }
        return $id; 
       
    }
    
    
}
