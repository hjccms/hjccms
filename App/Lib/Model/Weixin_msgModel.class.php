<?php
class Weixin_msgModel extends Model 
{
    
    function getInfo($condition,$field=null){
        $result = $this->field($field)->where($condition)->find();
        return $result;
    }
    
    function getMsg($condition,$order=null,$field=null){
        $result = $this->field($field)->where($condition)->order($order)->select();
        return $result;
    }
    
    //添加和更新菜单
    function addMsg($data)
    {
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
