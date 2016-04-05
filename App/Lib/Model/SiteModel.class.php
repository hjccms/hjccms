<?php
/*
 * 管理菜单模型
 */
class SiteModel extends Model 
{
    //自动完成
    protected $_auto = array( array('create_time','time',1,'function') );
    //站点信息  支持全部和单个
    function getSite($siteId='')
    {
        if($siteId&&  intval($siteId)>0) $condition = array('id'=>$siteId);
        $ret = $this->where($condition)->order("id asc")->select();
        if(!empty($siteId))            return $ret['0'];  //如果查询单个信息直接返回 数组
        else return $ret;
    }
    //排列子集
    function sortChilds($dataArr,$parentId)
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
                $result[$k]['childs'] = $this->sortChilds($dataArr , $v['id']);
            }
        }
        return $result;
    }
    //获取单个菜单信息
    function getInfo($id)
    {
        if(!$id) return '';
        $data['id'] = $id;
        $result = $this->where($data)->find();
        return $result;
    }
    //添加和更新菜单
    function addSite($data)
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
