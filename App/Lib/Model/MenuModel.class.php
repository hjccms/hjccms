<?php
/*
 * 管理菜单模型
 */
class MenuModel extends Model 
{
    //自动完成
    protected $_auto = array( array('create_time','time',1,'function') );
    //获取自级菜单 $status 为真 只查询 正常的菜单  否则全部查询  $child 为真 查询子菜单 否则不查询
    function getMenu($parentId='',$valid='',$child=true)
    {
        $condition = array();
        $parentId = $parentId?$parentId:0;
        if($valid!='') $condition['valid'] = $valid;
        $result = $this->where($condition)->order('sort asc,id asc')->select();
        //处理子集数据
        $ret = $this->sortChilds($result,$parentId);
        sort($ret);
        return $ret;
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
        D('Fileinfo')->changeFile($hash,$id,'menu');
        return $id; 
       
    }

    //获取功能列表
    function getList($menu,$flag=''){
        if(!$menu) return false;
        if(!$flag){
            $str .= '';
        }else{
            $str .= $flag.'├─ ';
        }
        $flag .= '&nbsp;&nbsp;&nbsp;&nbsp;';
        $tree =  null;
        foreach($menu as $v){
            if($v['valid'] == 0){
                $style = 'style="color:#ADADAD"';
            }
            $tree .= '<tr '.$style.'>';
            $tree .= '<td>'.$v['sort'].'</td>';
            $tree .= '<td>'.$str.$v['name'].'</td>';
            $tree .= '<td>'.U('Admin/'.$v['module'].'/'.$v['action'],$v['parameter']).'</td>';
            $tree .= '<td>'.get_valid($v['valid']).'</td>';
            $tree .= '<td><a href="'.U('Admin/Menu/menuAdd','id='.$v['id']).'" class="tablelink">修改</a>';
            $tree .= ' | <a href="#" class="tablelink  delLink" delid="'.$v['id'].'"> 删除</a></td>';
            $tree .= '</tr>';
            if(isset($v['childs'])){
                $tree .= $this->getList($v['childs'],$flag);
            }
        }
        return $tree;
    }
    
    
    function getLevel($parent_id){
        if(!isset($parent_id)) return false;
        $data['parent_id'] = $parent_id;
        $parentLevel = $this->where($data)->getField("level");
        if(!isset($parentLevel)) return false;
        $level = $parentLevel+1;
        return $level;
    }
}
