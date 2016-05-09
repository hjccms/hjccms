<?php
class Student_recordModel extends Model {
    
    protected $trueTableName = 'stu_student_record';    
    
    function getRecord($condition,$order){
        $result = $this->where($condition)->order($order)->select();
        foreach($result as $k=>$v){
            if($v['add_id']){
                $result[$k]['add_name'] = D("Admin")->where("id=".$v['add_id'])->getField("username");
            }
        }
        return $result;
    }
    
    function getRecordInfo($condition){
        $result = $this->where($condition)->find();
        return $result;
    }
    
    //添加和更新学生纪录
    function addStudentRecord($data){
        $hash = $data['__hash__'];
        if(!$this->create($data))
        {
            $msg = $this->getError();
            return $msg;
        }
        if($data['id']>0)
        {
            
            $this->save();
            $id = $data['id'];
        }
        else
        {
            $id = $this->add();
        }
        return $id; 
    }
    
    
}
