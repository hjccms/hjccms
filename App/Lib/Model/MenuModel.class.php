<?php
/*
 * 管理菜单模型
 */
class MenuModel extends Model 
{
    //自动完成
    protected $_auto = array( array('create_time','time',1,'function') );
    
    /**
     * 获取菜单数组
     * @param type $parentId  父id 值为空时查询全部菜单，否则查询该父id下子菜单
     * @param type $valid     是否查看有效菜单 值为空时查询所有菜单  值为true时只查询有效菜单
     * @param type $child     是否查询子菜单 值为ture时查询所有子菜单  值为false时只查询一层子菜单
     * @param type $type      菜单的类型 值为空查询所有类型  1链接  2内容按钮 3列表按钮
     * @return type           菜单数组
     */
    function getMenu($parentId='',$valid='',$child=true,$type=''){
        $condition = array();
        $parentId = $parentId?$parentId:0;
        if($valid!='') $condition['valid'] = $valid;
        $result = $this->where($condition)->order('sort asc,id asc')->select();
        //处理子集数据
        $ret = $this->sortChilds($result,$parentId,$child,$type);
        sort($ret);
        return $ret;
    }
    
    /**
     * 递归函数 查询多层子菜单
     * @param type $dataArr  菜单数组
     * @param type $parentId 父id
     * @param type $child    是否查询子菜单
     * @param type $type      菜单的类型
     * @return string        菜单数组
     */
    function sortChilds($dataArr,$parentId,$child,$type)
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
                        $result[$k]['childs'] = $this->sortChilds($dataArr , $v['id'],$child,$type);
                    }
                }
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
        $listButton = D('Menu')->getMenu($id,true,false,3);
        foreach($menu as $v){
            $listButtonTree = D('Menu')->getListButton($listButton,array('id'=>$v['id']));
            if($v['valid'] == 0){
                $style = 'style="color:#ADADAD"';
            }
            $tree .= '<tr '.$style.'>';
            $tree .= '<td>'.$v['id'].'</td>';
            $tree .= '<td>'.$str.$v['name'].'</td>';
            $tree .= '<td>'.get_menu_type($v['type']).'</td>';
            $url = $v['module']?U('Admin/'.$v['module'].'/'.$v['action'],$v['param']):'';
            $tree .= '<td>'.$url.'</td>';
            $tree .= '<td>'.$v['func'].'</td>';
            $tree .= '<td>'.get_valid($v['valid']).'</td>';
            $tree .= '<td>'.$listButtonTree.'</td>';
            $tree .= '</tr>';
            if(isset($v['childs'])){
                $tree .= $this->getListTree($v['childs'],$id,$flag);
            }
        }
        return $tree;
    }
    
    
    //获取top菜单树
    function getTopTree($menu){
        if(!$menu) return false;
        $tree =  null;
        foreach($menu as $v){
            $url = $v['module']?U('Admin/Index/left','id='.$v['id']):'';
            $tree .= '<li><a href="'.$url.'" target="leftFrame"><img src="'.$v['icon'].'" title="'.$v['name'].'" /><h2>'.$v['name'].'</h2></a></li>';
        }
        return $tree;
    }
    
    //获取left菜单树
    function getLeftTree($menu){
        if(!$menu) return false;
        $tree =  null;
        foreach($menu as $v){
            $param = $this->replaceParam(array('id'=>$v['id'],'param'=>$v['param']));
            $url = $v['module']?U('Admin/'.$v['module'].'/'.$v['action'],$param):'';
            $tree .= '<dd>';
            $tree .= '<div class="title">';
            $tree .= '<span><img src="'.$v['icon'].'"/></span>'.$v['name'];
            $tree .= '</div>';
            if(isset($v['childs'])){
                $tree .= '<ul class="menuson">';
                foreach($v['childs'] as $vc){
                    $childParam = $this->replaceParam(array('id'=>$vc['id'],'param'=>$vc['param']));
                    $childUrl = $vc['module']?U('Admin/'.$vc['module'].'/'.$vc['action'],$childParam):'';
                    $tree .= '<li><cite></cite><a href="'.$childUrl.'" target="rightFrame">'.$vc['name'].'</a><i></i></li>';
                }
                $tree .= '</ul>';
            } 
            $tree .= '</dd>';
        }
        return $tree;
    }
    
    
    /**
     * 获取内容按钮
     * @param type $menu  菜单 $menu = D('Menu')->getMenu($id,true,false,3); $id是父id
     * @return boolean|string
     */
    function getContentButton($menu){
        if(!$menu) return false;
        $tree =  null;
        foreach($menu as $v){
            $param = $this->replaceParam(array('id'=>$v['id'],'param'=>$v['param']));
            $url = $v['module']?U('Admin/'.$v['module'].'/'.$v['action'],$param):'';
            $tree .= '<li>';
            $tree .= '<a href="'.$url.'"><span><img src="'.$v['icon'].'" /></span>'.$v['name'].'</a>';
            $tree .= '</li>';
        }
        return $tree;
    }
    
    /**
     * 获取列表按钮
     * @param type $menu  菜单 $menu = D('Menu')->getMenu($id,true,false,2); 这里的id值得是父id
     * @param type $id    id 要替换的id值
     * @return boolean|string
     */
    function getListButton($menu,$arr){
        if(!$menu) return false;
        $tree =  null;
        foreach($menu as $k=>$v){
            $str = $k!=0?' | ':'';
            $paramArr = $arr;
            $paramArr['param'] = $v['param'];
            $param = $this->replaceParam($paramArr);
            $url = $v['module']?U('Admin/'.$v['module'].'/'.$v['action'],$param):'#';
            $func = $this->replaceFuncParam(array('id'=>$id,'func'=>$v['func']));
            $tree .= $str.'<a class="tablelink" href="'.$url.'" onclick="'.$func.'">'.$v['name'].'</a>';
        }
        return $tree;
    }
    
    //获取菜单名字
    function getNameById($id){
        if(!$id) return false;
        $name = $this->where("id=$id")->getField("name");
        return $name;
    }
    
    //获取位置
    function getPositionById($id){
        if(!$id) return false;
        $child = null;
        $menu = $this->getAllMenu();
        if(!$menu) return false;
        foreach($menu as $k=>$v){
            if($v["id"] == $id){
                $child = $v;
            }
        }
        if(!$child) return false;
        $parent = $this->getParentByChild($menu,$child);
        if(!$parent) return false;
        $position = null;
        foreach($parent as $k=>$v){
            $position .= '<li><a href="#">'.$v['name'].'</a></li>';
        }
        if(!$position) return false;
        $position .= '<li><a href="#">'.$child['name'].'</a></li>';
        return $position;
    }
    
    //获取所有有效菜单
    function getAllMenu(){
        $result = $this->where('valid=1')->order('sort asc,id asc')->select();
        return $result;
    }
    
    /**
     * 根据子信息获取多层父信息，返回父信息数组
     * @param type $menu       菜单
     * @param type $child      子信息 
     * @param type $parentArr  父信息数组
     * @return boolean         父信息数组
     */
    function getParentByChild($menu,$child,$parentArr=''){
        if(!$menu || !$child) return false;
        $parentArr = $parentArr?$parentArr:array();
        foreach($menu as $k=>$v){
            if($v['id'] == $child["parent_id"]){
               $parentArr[$v['level']]['id'] = $v['id'];
               $parentArr[$v['level']]['name'] = $v['name'];
               $parentArr[$v['level']]['parent_id'] = $v['parent_id'];
               $parentArr[$v['level']]['level'] = $v['level'];
               $parentArr = $this->getParentByChild($menu,$v,$parentArr);
            }
        }
        sort($parentArr);
        return $parentArr;
    }
    
    
    
    //获取id根据module和action
    function getIdByUrl($module,$action,$param=null){
        if(!$module || !$action) return false;
        $condition = array();
        $condition['module'] = $module;
        $condition['action'] = $action;
        if($param){
            $condition['param'] = $param;
        }
        $id = $this->where($condition)->getField("id");
        return $id;
    }
    
    //链接参数替换
    function replaceParam($arr){
        $menuParam = C('MENUPARAM');
        foreach($menuParam as $v){
            $arr['param']  = $arr['param']?str_replace('{'.$v.'}', $arr[$v], $arr['param']):'';
        }
        return $arr['param'];
    }
    
    //函数参数替换
    function replaceFuncParam($arr){
        $menuParam = C('MENUFUNCPARAM');
        foreach($menuParam as $v){
            $arr['func']  = $arr['func']?str_replace('{'.$v.'}', $arr[$v], $arr['func']):'';
        }
        return $arr['func'];
    }
}
