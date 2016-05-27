<?php
/*
 * 分站管理控制器
 */
class PageAction  extends BaseAction 
{
    function index()
    {
        $modelId = $this->_get('modelId'); 
        if(empty($modelId)||  intval($modelId)<=0) die('数据错误！');
        load('@.form');
        $modelInfo = D('Model')->getModel($modelId);
        //要显示的字段信息
        $fieldInfo = D('Fieldinfo')->getFields($modelId);
        $cateId = $this->_get('category_id');
        $info = D('Page')->getInfo($cateId);
        $formInput = formField($fieldInfo,$info);
        
       
        $this->fieldAddPlugins($formInput);
        
        
        $this->assign('formInput',$formInput);
        $this->assign('modelId',$modelId);
        $fromUrl = get_url();
        $this->assign('fromUrl',$fromUrl);
        $this->assign('info',$info);
        $this->assign('id',$info['id']);
        $this->display('Model:dataEdit');
    }
    //处理特殊的字段 需要加载的不同插件  以后如果需要加载其它的插件  都在这里统一添加
    function fieldAddPlugins($formInput)
    {
        $ldselect = ''; //联动菜单
        $editorPlugins = ''; //编辑器
        $uploadPlugins = ''; //单图上传按钮
        $dataPlugins = '';//日期插件
        foreach($formInput as $k=>$v)
        {
            if($v['inputType']=='ldselect')
            {
                $ldselect[$k] = $v['form_value'];
            }
            if($v['inputType']=='laydate')
            {
                $dataPlugins[$k] = $v;
            }
            if($v['inputType']=='upload')
            {
                $uploadPlugins[$k] = $v;
            }
            if($v['inputType']=='editor')
            {
                $editorPlugins[$k] = $v;
            }
        }
        
        $this->assign('ldselect',$ldselect);
        $this->assign('dataPlugins',$dataPlugins);
        $this->assign('uploadPlugins',$uploadPlugins);
        $this->assign('editorPlugins',$editorPlugins);
    }
    
}
