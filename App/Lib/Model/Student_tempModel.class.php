<?php
class Student_tempModel extends Model {
    
    protected $trueTableName = 'stu_student_temp';    
    
    function getInfo($condition,$field=null){
        $result = $this->field($field)->where($condition)->find();
        return $result;
    }
    
}
