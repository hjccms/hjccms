<?php
/*
 * 管理员控制器
 */
class RegionAction  extends BaseAction 
{
    function index($dataList)
    {
        if(empty($dataList)) return '';
        foreach($dataList as $k=>$v)
        {
            $ids .= $v['id'].',';
        }
        $ids = trim($ids,',');
        $parents = M('Region')->where("id in (".$ids.")")->getField('id,parent_id');
        foreach($dataList as $k=>$v)
        {
            $dataList[$k]['parent_id'] = $parents[$v['id']];
        }
        $dataList = sortChilds($dataList, 0);
        
        return $dataList;
    }
    
    
}
