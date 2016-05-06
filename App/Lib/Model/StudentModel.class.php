<?php
class StudentModel extends Model {
    
    //获取单个学生详细信息
    function getStudentInfo($condition,$field=null){
        $result = $this->field($field)->join("left join sys_student_info on sys_student.id=sys_student_info.student_id")->where($condition)->find();
        return $result;
    }
    
    //获取多个学生详细信息
    function getStudent($condition,$order=null,$field=null){
        $result = $this->field($field)->join("left join sys_student_info on sys_student.id=sys_student_info.student_id")->where($condition)->order($order)->select();
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
    
    //添加和更新学生详细信息
    function addStudentInfo($data){
        if($data['id']){
            D("Student_info")->save($data);
            $id = $data['student_id'];
        }else{
            $id = D("Student_info")->add($data);
        }
        return $id; 
       
    }
    
    
}
