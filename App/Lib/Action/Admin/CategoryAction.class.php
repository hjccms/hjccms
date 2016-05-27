<?php
/*
 * 分站管理控制器
 */
class CategoryAction  extends BaseAction 
{
    function index($dataList,$tableName)
    {
        if(empty($dataList)) return '';
        foreach($dataList as $k=>$v)
        {
            $ids .= $v['id'].',';
        }
        $ids = trim($ids,',');
        $parents = M(ucfirst($tableName))->where("id in (".$ids.")")->getField('id,mid,parent_id');
        foreach($dataList as $k=>$v)
        {
            $dataList[$k]['parent_id'] = $parents[$v['id']]['parent_id'];
            $dataList[$k]['mid'] = $parents[$v['id']]['mid'];
        }
       
        $dataList = sortChilds($dataList, 0);
        
        return $dataList;
    }
    function selTems()
    {
        $modelId = $this->_post('modelId');
        
        $tem = $this->getModelTems($modelId);
        
        $start = '<option value="">请选择</option>';
        foreach($tem as $k=>$v)
        {
            foreach($v as $key=>$val)
            {
                $result[$k] .=  '<option value="'.$val['id'].'">'.$val['name'].'</option>';
            }
        }
        foreach($result as $k=>$v)
        {
            $result[$k] = $start.$v;
        }
        
        if(empty($result['index'])) $result['index'] = $start;
        if(empty($result['list'])) $result['list'] = $start;
        if(empty($result['show'])) $result['show'] = $start;
        
        $this->ajaxReturn($result,'suc',1);
        
    }
    //根据模型ID 获取可用模板
    function getModelTemType($type)
    {
        $id = $this->_get('id');
        $info = D('Category')->getInfo($id);
        $modelId = $info['mid'];
        $result = $this->getModelTems($modelId);
        $ret = $result[$type];
        return $ret;
    }
    
    //获取所有模板
    function getModelTems($modelId)
    {
        $modelInfo = D('Model')->getModel($modelId);
        $tableName = $modelInfo['table_name'];
        $template  = $this->adminSiteInfo->template;
      
        $dir = './App/Tpl/'.$template.'/Index';
        $files = readFiles($dir);
       
        foreach($files as $k=>$v)
        {
            if(strpos($v, $tableName.'_index')===0)
            {
                $tem['index'][] = array('id'=>$v,'name'=>$v);
            }
            if(strpos($v, $tableName.'_list')===0)
            {
                $tem['list'][] = array('id'=>$v,'name'=>$v);
            }
            if(strpos($v, $tableName.'_show')===0)
            {
                $tem['show'][] = array('id'=>$v,'name'=>$v);
            }
        }
        
        return $tem;
    }
    //增加单页内容 获取栏目
    function getCateName($cateId)
    {
        if(!$cateId) $cateId = $this->_get('category_id');
        $info = D('Category')->getInfo($cateId);
        $ret[0]['id'] = $info['id'];
        $ret[0]['name'] = $info['name'];
        $ret[0]['selected'] = 1;
        $ret['noStartStr'] = 1;
        return $ret;
    }
}
