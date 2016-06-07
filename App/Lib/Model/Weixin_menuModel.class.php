<?php
class Weixin_menuModel extends Model 
{
    //自动完成
    protected $_auto = array( array('create_time','time',1,'function') );
    
    function getMenu($condition,$parentId='',$child=true){
        $parentId = $parentId?$parentId:0;
        $result = $this->where($condition)->order('sort asc,id asc')->select();
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
    
    function getInfo($condition,$field=null){
        $result = $this->field($field)->where($condition)->find();
        return $result;
    }
    
    function addMenu($data)
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
    
    //获取菜单级别
    function getLevel($parent_id){
        if(!isset($parent_id)) return false;
        $data['id'] = $parent_id;
        $parentLevel = $this->where($data)->getField("level");
        if(!isset($parentLevel)) return false;
        $level = $parentLevel+1;
        return $level;
    }

    //获取功能列表菜单树
    function getListTree($menu,$id,$flag=''){
        if(!$menu) return false;
        if(!$flag){
            $str .= '';
        }else{
            $str .= $flag.'├─ ';
        }
        $flag .= '&nbsp;&nbsp;&nbsp;&nbsp;';
        $tree =  null;
        $listButton = D("Menu")->getMenu($id,true,false,3);
        foreach($menu as $v){
            $listButtonTree = D("Menu")->getListButton($listButton,array('id'=>$v['id']));
            $tree .= '<tr id="'.$v['id'].'">';
            $tree .= '<td>'.$v['id'].'</td>';
            $tree .= '<td>'.$str.$v['name'].'</td>';
            $tree .= '<td>'.$v['keyword'].'</td>';
            $tree .= '<td>'.$v['url'].'</td>';
            $tree .= '<td>'.get_valid($v['valid']).'</td>';
            $tree .= '<td>'.$listButtonTree.'</td>';
            $tree .= '</tr>';
            if(isset($v['childs'])){
                $tree .= $this->getListTree($v['childs'],$id,$flag);
            }
        }
        return $tree;
    }
}
