<?php
class DispatchModel extends Model {
    
    function getDispatch($condition,$region='false'){
        $result = $this->where($condition)->order('id asc')->select();
        if(!$region){
            return $result;
        }
        $region = D("Region")->getRegion('valid=1 and del is null');
        foreach($result as $k=>$v){
             $result[$k]['parent_arr'] = D("Region")->getParentByChild($region,$v['region_id']);
        }
        return $result;
    }
    
    
    
}
