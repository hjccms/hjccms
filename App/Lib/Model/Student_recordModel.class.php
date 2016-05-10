<?php
class Student_recordModel extends Model {
    
    protected $trueTableName = 'stu_student_record';    
    
    function getRecord($condition,$order){
        $result = $this->where($condition)->order($order)->select();
        foreach($result as $k=>$v){
            if($v['add_id']){
                $result[$k]['add_name'] = D("Admin")->where("id=".$v['add_id'])->getField("username");
            }
            if($v['other_content']){
                $other_content = json_decode($v['other_content'],true);
                $result[$k]['speed'] = $other_content['speed']; 
                $result[$k]['intention'] = $other_content['intention']; 
                $result[$k]['course'] = $other_content['course']; 
                $result[$k]['record_name'] = $other_content['record_name']; 
                $result[$k]['record_mobile'] = $other_content['record_mobile']; 
            }
        }
        return $result;
    }
    
    function getRecordInfo($condition){
        $result = $this->where($condition)->find();
        if($result['other_content']){
            $other_content = json_decode($result['other_content'],true);
            $result['speed'] = $other_content['speed']; 
            $result['intention'] = $other_content['intention']; 
            $result['course'] = $other_content['course']; 
            $result['record_name'] = $other_content['record_name']; 
            $result['record_mobile'] = $other_content['record_mobile']; 
        }
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
