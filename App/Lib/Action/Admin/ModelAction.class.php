<?php
/*
 * 分站管理控制器
 */
class ModelAction  extends BaseAction 
{
    function index()
    {
        $this->getContentButton();
        $this->getListButton();
        $models = D('Model')->getModel();
        $this->assign('models',$models);
        $this->display();
    }
    
    //添加菜单
    function modelAdd()
    {
        $id = $this->_get('id');

        load('@.form');
        $this->display();
    }
    //修改功能
    function modelEdit()
    {
        $id = $this->_get('id');
      
        //info信息
        if($id>0) $info = D('Model')->getModel($id);
        else $info = array();
        $this->assign('info',$info);
        $this->assign('id', $id);
        load('@.form');
        $this->display();
    }
    function ajaxPost()
    {
        if(!IS_POST) $this->ajaxReturn ('','非法请求！',0);
        $post = $this->_post();
        
        //去模型处理其它参数
        $ret = D('Model')->addModel($post);
        if(intval($ret)>0) $this->ajaxReturn ('','Success！',1);
        else $this->ajaxReturn ('',$ret,0);
    }
    function checkName()
    {
        $post = $this->_post();
        $id = $this->_get('id');
        if(intval($id)>0) $condition['_string'] = "id!='$id'";
        $ret = D('Model')->checkField($post['name'],$post['param'],$condition);
        
        if($ret) $ret = array('info'=>'数据重复！','status'=>'n');
        else  $ret = array('info'=>'验证成功！','status'=>'y');
        die(json_encode($ret));
    }
    
    //自定义模型数据列表
    function dataList()
    {
        $modelId = $this->_get('modelId'); 
        if(empty($modelId)||  intval($modelId)<=0) die('数据错误！');
        
        $modelInfo = D('Model')->getModel($modelId);
        //要显示的字段信息
        $fieldInfo = D('Fieldinfo')->getField($modelId,array('list_show'=>1));
        $fields = 'id,valid,create_time,';
        foreach($fieldInfo as $k=>$v)
        {
            $fields .= $v['field_name'].',';
            $fieldNewInfo[$v['field_name']] = $v;
        }
        $fields = substr ($fields, 0,strlen($fields)-1);
        $tableModel = M(ucfirst($modelInfo['table_name']));
        $condition = array('_string'=>"del is null");
        //是否开启分页
        if($modelInfo['page_open']==1)
        {
            $page = $this->_get('page');
            $page = $page?intval($page):1; //当前页
            $this->assign('page',$page);
            $listCount =  $tableModel->where($condition)->count();
            $startNum = $modelInfo['page_num']*($page-1);
            $pageNum = ($listCount%$modelInfo['page_num']==0)?($listCount/$modelInfo['page_num']):($listCount/$modelInfo['page_num']+1);
            
            $dataList = $tableModel->where($condition)->field($fields)->order("id asc")->limit($startNum,$modelInfo['page_num'])->select();
        }
        else
        {
            $dataList = $tableModel->where($condition)->field($fields)->order("id asc")->select();
        }
        $this->assign('page',$page?$page:'');
        $this->assign('pageNum',$pageNum?$pageNum:'');
        //处理一些特殊的字段
        load('@.form');
        foreach($dataList as $k=>$v)
        {
            foreach($v as $key=>$val)
            {
                
                if($fieldNewInfo[$key]['type']=='radio'||$fieldNewInfo[$key]['type']=='select')
                {
                    $dataList[$k][$key] = getRadioValue($fieldNewInfo[$key]['form_value'], $val);
                }
            }
        }
      
        $this->getContentButton();
        $this->getListButton();
        $this->assign('modelId',$modelId);
        $this->assign('fieldInfo',$fieldInfo);
        $this->assign('dataList',$dataList);
        $this->display();
    }
    
    //数据添加
    function dataAdd()
    {
        $modelId = $this->_get('modelId'); 
        if(empty($modelId)||  intval($modelId)<=0) die('数据错误！');
        load('@.form');
        $modelInfo = D('Model')->getModel($modelId);
        //要显示的字段信息
        $fieldInfo = D('Fieldinfo')->getField($modelId);
        $formInput = formField($fieldInfo);
        $this->assign('formInput',$formInput);
        $this->assign('modelId',$modelId);
        
        $this->display();
    }
    //修改数据
    function  dataEdit()
    {
        $modelId = $this->_get('modelId'); 
        $id =  $this->_get('id'); 
        if(empty($id)||  intval($id)<=0) die('数据错误！');
        load('@.form');
        if(intval($modelId)>0)
        {
            $modelInfo = D('Model')->getModel($modelId);
            $tableModel = M(ucfirst($modelInfo['table_name']));
        }
        else
        {
            $tableModel = M(ucfirst($this->_get('table_name')));
        }
        
        $info = $tableModel->where("id='$id'")->find();
        //要显示的字段信息
        $fieldInfo = D('Fieldinfo')->getField($modelId);
        $formInput = formField($fieldInfo,$info);
        $this->assign('formInput',$formInput);
        $this->assign('modelId',$modelId);
        $this->assign('id',$id);
        $this->display();
    }
    //提交数据
    function dataAjaxPost()
    {
        if(!IS_POST) $this->ajaxReturn ('','非法请求！',0);
        $post = $this->_post();
        $modelId = $this->_get('modelId');
        if(intval($modelId)>0)
        {
            $modelInfo = D('Model')->getModel($modelId);
            $tableModel = M(ucfirst($modelInfo['table_name']));
            
        }
        else
        {
            $tableModel = M(ucfirst($this->_get('table_name')));
        }
        //导入外部处理机制
        if($post['importAction']&&$post['importFun'])
        {
            $post['importAction'] = ucfirst($post['importAction']);
            import("@.Action.Admin.".$post['importAction']);
            $model = new $post['importAction']();
            $post = $model->$post['importFun']($post);
            
        }
        unset($post['importAction']);
        unset($post['importFun']);
        $hash = $post['__hash__'];
        $post['create_time'] = time();
        if(!$tableModel->create($post))
        {
            $msg = $tableModel->getError();
            $this->ajaxReturn ('',$msg,0);;
        }
        if($post['id']>0)
        {
            if(!$tableModel->save()) $this->ajaxReturn ('','您没有修改数据或者发生错误！',0);
            else $this->ajaxReturn ('','修改成功！',1);
        }
        else
        {
            if($id = $tableModel->add())
            {
               
                $this->ajaxReturn ('','添加成功！',1);
            }
            else
            {
                $this->ajaxReturn ('','添加失败！',0);
            }
        }
    }
    //删除数据方法
    /*
     * 支持真删除和修改del参数
     */
    function dataDel()
    {
        if(!IS_POST) $this->ajaxReturn ('','非法请求！',0);
        $id = $this->_param('id');
        $modelId = $this->_get('modelId');
        if(intval($modelId)>0)
        {
            $modelInfo = D('Model')->getModel($modelId);
            $tableModel = M(ucfirst($modelInfo['table_name']));
        }
        else
        {
            $tableModel = M(ucfirst($this->_get('table_name')));
        }
      
        $del = $this->_get('del');
        $condition = array('id'=>$id);
        if($del==1)  //真删除
        {
            $tableModel->where($condition)->delete();
        }
        else
        {
            $data['del'] = 1;
            $tableModel->where($condition)->save($data);
        }
        $this->ajaxReturn('','删除成功！',1);
    }
    //验证数据唯一性 通用方法
    function checkFieldOnly()
    {
        $post = $this->_post();
        $id = $this->_get('id');
        $modelId = $this->_get('modelId');
        if(intval($modelId)>0)
        {
            $modelInfo = D('Model')->getModel($modelId);
            $tableModel = M(ucfirst($modelInfo['table_name']));
            
        }
        else
        {
            $tableModel = M(ucfirst($this->_get('table_name')));
        }
        $condition = array($post['name']=>$post['param']);
        if(intval($id)>0) $condition['_string'] = "id!='$id'";
        $ret = $tableModel->where($condition)->getField('id');
        if($ret) $ret = array('info'=>'数据重复！','status'=>'n');
        else  $ret = array('info'=>'验证成功！','status'=>'y');
        die(json_encode($ret));
    }
}
