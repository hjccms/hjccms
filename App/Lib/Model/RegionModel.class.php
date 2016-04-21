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
    
    
    function getRegionList($site_id,$parentId='',$valid='',$child=true,$region_ids=null){
        if(!$site_id) return false;
        $condition = array();
        $parentId = $parentId?$parentId:0;
        if($valid!='') $condition['valid'] = $valid;
        if($site_id!='') $condition['site_id'] = $site_id;
        $result = $this->where($condition)->order('sort asc,id asc')->select();
        if($region_ids){
            $region_ids = explode(",", $region_ids);
            $tmp = null;
            foreach($result as $k=>$v){
                if(in_array($v['id'], $region_ids)){
                    $tmp[$k] = $v;
                }
            }
            $result = null;
            $result = $tmp;
        }
        
        //处理子集数据
        $ret = $this->sortChilds($result,$parentId,$child);
        sort($ret);
        return $ret;
    }
    
    function sortChilds($dataArr,$parentId,$child)
    {
        if(!is_array($dataArr)||empty($dataArr)) return '';
        foreach ($dataArr as $k=>$v)
        {
            $allParents[$k] = $v['parent_id'];
        }
        if(!in_array($parentId,$allParents)) return ''; 
        foreach ($dataArr as $k=>$v)
        {
            if($v['parent_id']==$parentId)
            {
                $result[$k] = $v;
                if($child){
                    $result[$k]['childs'] = $this->sortChilds($dataArr , $v['id'],$child);
                }
            }
        }
        return $result;
    }
    
}
