<?php
class DispatchModel extends Model {
    
    function getDispatch($condition){
        $result = $this->where($condition)->order('id asc')->select();
        return $result;
    }
    
    
    
}
