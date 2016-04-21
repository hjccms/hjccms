<?php
class DispatchModel extends Model {
    
    function getDispatch($condition,$region='false'){
        $result = $this->where($condition)->order('id asc')->select();
        if(!$region){
            return $result;
        }
        return $result;
    }
    
    
    
}
