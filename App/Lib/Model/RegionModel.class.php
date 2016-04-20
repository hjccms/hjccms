<?php
class RegionModel extends Model {
    
    function getRegion($condition){
        $result = $this->where($condition)->order('id asc')->select();
        return $result;
    }
    
    
    function getParentByChild($region,$child,$parentArr=''){
        if(!$region || !$child) return false;
        $parentArr = $parentArr?$parentArr:array();
        foreach($region as $k=>$v){
            if($v['id'] == $child["parent_id"]){
               $parentArr[$k]['id'] = $v['id'];
               $parentArr[$k]['name'] = $v['name'];
               $parentArr[$k]['parent_id'] = $v['parent_id'];
               $parentArr = $this->getParentByChild($region,$v,$parentArr);
            }
        }
        return $parentArr;
    }
}
