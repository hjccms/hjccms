<?php
class StudentModel extends Model {
    
    protected $trueTableName = 'stu_student';    
    
    //获取单个学生详细信息
    function getStudentInfo($condition,$field=null){
        $result = $this->field($field)->join("left join stu_student_info on stu_student.id=stu_student_info.student_id")->where($condition)->find();
        return $result;
    }
    
    //获取多个学生详细信息
    function getStudent($condition,$order=null,$field=null){
        $result = $this->field($field)->join("left join stu_student_info on stu_student.id=stu_student_info.student_id")->where($condition)->order($order)->select();
        return $result;
    }
    
    //添加和更新学生
    function addStudent($data){
        if(!$this->create($data)){
            $msg = $this->getError();
            return $msg;
        }
        if($data['id']>0){
            $this->save();
            $id = $data['id'];
        }else{
            $id = $this->add();
        }
        return $id; 
       
    }
}
