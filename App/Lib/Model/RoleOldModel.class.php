<?php
/*
 * 角色模型
 */
class RoleOldModel extends Model {
    
    /**
     * 获取角色数组
     * @param type $parentId  父id 值为空时查询全部角色，否则查询该父id下子角色
     * @param type $valid     是否查看有效角色 值为空时查询所有角色  值为true时只查询有效角色
     * @param type $child     是否查询子角色 值为ture时查询所有子角色  值为false时只查询一层子角色
     * @param type $type      角色的类型 值为空查询所有类型  1超级管理员  2功能管理员 3权限管理员
     * @return type           角色数组
     */
    function getRole($parentId='',$valid='',$child=true,$type=''){
        $condition = array();
        $parentId = $parentId?$parentId:0;
        if($valid!='') $condition['valid'] = $valid;
        $result = $this->where($condition)->order('id asc')->select();
        //处理子集数据
        $ret = $this->getChilds($result,$parentId,$child,$type);
        sort($ret);
        return $ret;
    }
    
    /**
     * 递归函数 查询多层子角色
     * @param type $dataArr  角色数组
     * @param type $parentId 父id
     * @param type $child    是否查询子角色
     * @param type $type      角色的类型
     * @return string        角色数组
     */
    function getChilds($dataArr,$parentId,$child,$type)
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
                if(!$type || ($type && $v['type']==$type)){
                    $result[$k] = $v;
                    if($child){
                        $result[$k]['childs'] = $this->getChilds($dataArr , $v['id'],$child,$type);
                    }
                }
            }
        }
        return $result;
    }
    
    //获取单个角色信息
    function getInfo($id)
    {
        if(!$id) return '';
        $data['id'] = $id;
        $result = $this->where($data)->find();
        return $result;
    }
    
    //添加和更新角色
    function addRole($data)
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
    

    //获取角色
    function getListTree($role,$id,$flag=''){
        if(!$role) return false;
        if(!$flag){
            $str .= '';
        }else{
            $str .= $flag.'├─ ';
        }
        $flag .= '&nbsp;&nbsp;&nbsp;&nbsp;';
        $tree =  null;
        $listButton = D('Menu')->getMenu($id,true,false,3);
        foreach($role as $v){
            $listButtonTree = D('Menu')->getListButton($listButton,array('id'=>$v['id']));
            if($v['valid'] == 0){
                $style = 'style="color:#ADADAD"';
            }
            $tree .= '<tr '.$style.'>';
            $tree .= '<td>'.$v['id'].'</td>';
            $tree .= '<td>'.$str.$v['name'].'</td>';
            $tree .= '<td>'.get_role_type($v['type']).'</td>';
            $tree .= '<td>'.get_valid($v['valid']).'</td>';
            $tree .= '<td>'.$listButtonTree.'</td>';
            $tree .= '</tr>';
            if(isset($v['childs'])){
                $tree .= $this->getListTree($v['childs'],$id,$flag);
            }
        }
        return $tree;
    }
    
    
    function getSites($site_ids=null){
        $arr = D("Site")->where('valid=1')->select();
        if(!$site_ids){
            return $arr;
        }
        $site_ids = explode(",", $site_ids);
        $re = null;
        foreach($arr as $k=>$v){
            if(in_array($v['id'], $site_ids)){
                $re[$k] = $v;
            }
        }
        return $re;
    }
    
    //获取单个角色详细信息
    function getRoleInfo($condition){
        $result = $this->where($condition)->find();
        return $result;
    }
}
