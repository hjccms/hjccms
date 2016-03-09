<?php
/*
 * 管理菜单模型
 */
class MenuModel extends Model 
{
    //获取自级菜单 $status 为真 只查询 正常的菜单  否则全部查询  $child 为真 查询子菜单 否则不查询
    function getMenu($id='',$status=false,$child=true)
    {
        if($id=='') $parentId='0'; else $parentId=$id;
        $data['parentId'] = $parentId;
        if($status==true) $data['status'] = '1';
        $result = $this->where($data)->order('sort asc,id asc')->select();
        if($result&&$child)
        {
            foreach($result as $k=>$v)
            {
                $result[$k]['childs'] = $this->getMenu($v['id'],$status,$child);
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
    function addMenu($data)
    {
        if($data['id']>0)
        {
            $this->data($data)->save();
            $id = $data['id'];
        }
        else
        {
            $data['createTime'] = time();
            $id = $this->add($data);
        }
        return $id;
    }
}
