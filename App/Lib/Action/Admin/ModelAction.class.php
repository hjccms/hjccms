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
        $modelType = C('MODELFUNTYPE');
        $this->assign('modelType',$modelType);
        $models = D('Model')->getModel();
        $this->assign('models',$models);
        $this->display();
    }
    
    //添加菜单
    function modelAdd()
    {
        $id = $this->_get('id');

        load('@.form');
        //获取可用的列表模板
        $dir = './App/Tpl/Admin/Model';
        $files = readFiles($dir);
        foreach($files as $k=>$v)
        {
            if(strpos($v, 'dataList')===0)
            {
                $tem[] = array('id'=>$v,'name'=>$v);
            }
        }
        $config = C('MODELFUNTYPE');
        foreach($config as $k=>$v)
        {
            $modelType[$k]['id'] = $k;
            $modelType[$k]['name'] = $v;
        }
        $this->assign('modelType',$modelType);
        $this->assign('tem',$tem);
        
        $this->display();
    }
    //修改功能
    function modelEdit()
    {
        $id = $this->_get('id');
        //获取可用的列表模板
        $dir = './App/Tpl/Admin/Model';
        
        $files = readFiles($dir);
        foreach($files as $k=>$v)
        {
            if(strpos($v, 'dataList')===0)
            {
                $tem[] = array('id'=>$v,'name'=>$v);
            }
        }
        $config = C('MODELFUNTYPE');
        foreach($config as $k=>$v)
        {
            $modelType[$k]['id'] = $k;
            $modelType[$k]['name'] = $v;
        }
        $this->assign('modelType',$modelType);
        $this->assign('tem',$tem);
        //info信息
        if($id>0) $info = D('Model')->getModel($id);
        else $info = array();
        $this->assign('fromUrl',$_GET['fromUrl']);
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
        load('@.form');
        //搜索显示
        $searchFiled = D('Fieldinfo')->getFields($modelId,array('search_show'=>1));
      
        foreach($searchFiled as $k=>$v)
        {
            if($this->_get($v['field_name']))
            {
                $get = $this->_get($v['field_name']);
                $searchCon[$v['field_name']] = array('like','%'.$get.'%');
            }
        }
        
     
        $formInput = formField($searchFiled,$this->_get());
        $ldselect = '';
        foreach($formInput as $k=>$v)
        {
            if($v['inputType']=='ldselect')
            {
                $ldselect[$k] = $v['form_value'];
            }
        }
        $this->assign('ldselect',$ldselect);
        $this->assign('formInput',$formInput);
        
        
        $modelInfo = D('Model')->getModel($modelId);
        //要显示的字段信息
        $fieldInfo = D('Fieldinfo')->getFields($modelId,array('list_show'=>1));
        $fields = 'id,valid,create_time,sort,';
        foreach($fieldInfo as $k=>$v)
        {
            $fields .= $v['field_name'].',';
            $fieldNewInfo[$v['field_name']] = $v;
        }
        $fields = substr ($fields, 0,strlen($fields)-1);
        $tableModel = M(ucfirst($modelInfo['table_name']));
        $condition = array('_string'=>"del is null");
        $con = $this->getSiteCondition();
        if(!empty($con)) $condition = array_merge ($condition,$con);
        if(!empty($searchCon)) $condition = array_merge ($condition,$searchCon);
       
        //是否开启分页
        if($modelInfo['page_open']==1)
        {
            $page = $this->_get('page');
            $page = $page?intval($page):1; //当前页
            $this->assign('page',$page);
            $listCount =  $tableModel->where($condition)->count();
            $startNum = $modelInfo['page_num']*($page-1);
            $pageNum = ($listCount%$modelInfo['page_num']==0)?($listCount/$modelInfo['page_num']):($listCount/$modelInfo['page_num']+1);
            
            $dataList = $tableModel->where($condition)->field($fields)->order("sort asc,id desc")->limit($startNum,$modelInfo['page_num'])->select();
        }
        else
        {
            $dataList = $tableModel->where($condition)->field($fields)->order("sort asc,id desc")->select();
        }
       
        $this->assign('page',$page?$page:'');
        $this->assign('pageNum',$pageNum?$pageNum:'');
        //处理一些特殊的字段
        
        foreach($dataList as $k=>$v)
        {
            foreach($v as $key=>$val)
            {
                if($fieldNewInfo[$key]['type']=='radio'||$fieldNewInfo[$key]['type']=='select')
                {
                    $dataList[$k][$key] = getRadioValue($fieldNewInfo[$key]['form_value'], $val);
                }
                
                if($fieldNewInfo[$key]['type']=='ldselect')
                {
                    if(!$GLOBALS['ld'.$fieldNewInfo[$key]['form_value']])
                    {
                        $siteId = $this->adminInfo->site_id;
                        $ldInfo = M(ucfirst($fieldNewInfo[$key]['form_value']))->where(array('site'=>$siteId))->getField('id,name');
                        $GLOBALS['ld'.$fieldNewInfo[$key]['form_value']] = $ldInfo;
                    }
                    
                    $dataList[$k][$key] = $GLOBALS['ld'.$fieldNewInfo[$key]['form_value']][$val];
                }
            }
        }
        //导入外部处理机制
        $importAction = $this->_get('importAction');
        $importFun = $this->_get('importFun');
        if($importAction&&$importFun)
        {
            $importAction = ucfirst($importAction);
            import("@.Action.Admin.".$importAction);
            $model = new $importAction();
            $dataList = $model->$importFun($dataList);
        }
        
        
        $this->getContentButton();
        $this->getListButton();
        if(!empty($_GET['fromUrl'])) $this->assign('fromUrl',$_GET['fromUrl']);
        else $this->assign('fromUrl',encrypt(urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']),'E'));
        $this->assign('modelId',$modelId);
        $this->assign('fieldInfo',$fieldInfo);
        $this->assign('dataList',$dataList);
        if($modelInfo['template']) $template = explode('.',$modelInfo['template']);
        else $template['0'] = 'dataList';
        $this->display($template['0']);
    }
    
    //数据添加
    function dataAdd()
    {
        $modelId = $this->_get('modelId'); 
        if(empty($modelId)||  intval($modelId)<=0) die('数据错误！');
        load('@.form');
        $modelInfo = D('Model')->getModel($modelId);
        //要显示的字段信息
        $fieldInfo = D('Fieldinfo')->getFields($modelId);
        $formInput = formField($fieldInfo);
        
        $ldselect = '';
        foreach($formInput as $k=>$v)
        {
            if($v['inputType']=='ldselect')
            {
                $ldselect[$k] = $v['form_value'];
            }
        }
        $this->assign('ldselect',$ldselect);
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
        $fieldInfo = D('Fieldinfo')->getFields($modelId);
        $formInput = formField($fieldInfo,$info);
        $ldselect = '';
        foreach($formInput as $k=>$v)
        {
            if($v['inputType']=='ldselect')
            {
                $ldselect[$k] = $v['form_value'];
            }
        }
        
        $this->assign('ldselect',$ldselect);
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
        $post['create_time'] = time();
        $post['admin_id'] = $this->adminInfo->id;
        $post['site_id'] = $this->adminInfo->site_id;
        $fromUrl = urldecode(encrypt($post['fromUrl'],'D'));
        unset($post['fromUrl']);
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
        
        if(!$tableModel->create($post))
        {
            $msg = $tableModel->getError();
            $this->ajaxReturn ('',$msg,0);;
        }
        if($post['id']>0)
        {
            
            
            if(!$tableModel->save())
            {
                $this->ajaxReturn ('','您没有修改数据或者发生错误！',0);
            }
            else
            {
                $ret['status'] = 1;
                $ret['info'] = '修改成功！';
                if(!empty($fromUrl))
                {
                    $ret['url'] = $fromUrl;
                    $ret['status'] = 2;
                }
                $this->ajaxReturn ($ret,'JSON');
            }
        }
        else
        {
            if($id = $tableModel->add())
            {
                $ret['status'] = 1;
                $ret['info'] = '添加成功！';
                if(!empty($fromUrl)) $ret['url'] = $fromUrl;
                $this->ajaxReturn ($ret,'JSON');
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
        //如果存在子级有数据的情况 不能删除
        
        $fields = $tableModel->getDbFields();
        if(in_array('parent_id', $fields))
        {
            $childs = $tableModel->where('parent_id='.$id.' and del is null')->getField('id');
            if($childs) $this->ajaxReturn('','请先删除此条数据下面的子数据！',0);
        }
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
    //数据排序
    function dataSort()
    {
        if(!IS_POST) $this->ajaxReturn ('','非法请求！',0);
        //$id = $this->_param('id');
        $modelId = $this->_param('modelId');
        if(intval($modelId)>0)
        {
            $modelInfo = D('Model')->getModel($modelId);
            $tableModel = M(ucfirst($modelInfo['table_name']));
        }
        else
        {
            $tableModel = M(ucfirst($this->_param('table_name')));
        }
        $post = $this->_post();
        $i = 0;
        foreach($post as $k=>$v)
        {
            if(strpos($k, '__')===false)                continue;
            $arr = explode('__',$k);
            $condition[$i]['id'] = $arr['1'];
            $condition[$i][$arr['0']] = $v;
            $i ++;
        }
        foreach($condition as $k=>$v)
        {
            $tableModel->save($v);
        }
        $this->ajaxReturn('','修改成功！',1);
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
    //联动字段 获取下一级菜单
    function getLdChilds()
    {
        if(!IS_AJAX) $this->ajaxReturn('','错误请求！',0);
        $post = $this->_post();
        if($post['id']=='') $this->ajaxReturn($post['level'],'',1); 
        $modelName = ucfirst($post['modelName']);
        $result = D('Model')->getSelAll($modelName,$post['id'],false);
        if(empty($result)) $this->ajaxReturn($post['level'],'',1);
        foreach($result as $k=>$v)
        {
          
            $optionStr .= '<option value="'.$v['id'].'"  >'.$v['name'].'</option>';
            
        }
        $level = $post['level']+1;
        $startOption = '<option value=""  >请选择</option>';
        $str =  '&nbsp;&nbsp;<select class="select1 ldselchild'.$post['modelName'].'" style="width:100px;" datatype="*"  name="'.$post['modelName'].'_id" level='.$level.'  id="ld'.$post['modelName'].'" >
                        '.$startOption.$optionStr.'
                    </select>';  
        $this->ajaxReturn($level-1,$str,1);
    }
}
