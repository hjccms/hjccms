<?php
class Weixin_configModel extends Model{
    
    
    
    
    function getInfo($condition,$field=null){
        $result = $this->field($field)->where($condition)->find();
        return $result;
    }
    

}
