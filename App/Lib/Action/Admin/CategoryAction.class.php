<?php
/*
 * 分站管理控制器
 */
class CategoryAction  extends BaseAction 
{
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
        $modelId = $info['content_type'];
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
}
