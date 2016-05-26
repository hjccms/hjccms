<?php

/*
 * 自定义表单函数库 
 */
/**
 * @param data['isMust'] bool         是否必须       默认为false
 * @param data['title'] string        表单标题         
 * @param data['inputName'] string    表单字段名     不能为空    只能为英文
 * @param data['value'] string        表单字段初始值  
 * @param data['addClass'] string     表单附加class
 * @param data['addId'] string        表单附加id        
 * @param data['addHtml'] string      表单附加html   一般为表单验证附加的参数    
 * @param data['tip'] string          表单默认值      
 * @param data['li_style'] string     表单外li的style   一般用于display:none 
 * @param data['li_class'] string     表单外li的class 
 * @return string
 */
//普通文本框
function input($data)
{
    if($data['isMust']!=false) $data['isMustStr'] = '<b>*</b>'; else $data['isMustStr']='';
    $str = '<li style="'.$data['li_style'].'" class="'.$data['li_class'].'"><label>'.$data['title'].$data['isMustStr'].'</label><input name="'.$data['inputName'].'" value="'.$data['value'].'" type="text" tip＝"'.$data['tip'].'" class="dfinput '.$data['addClass'].'" id="'.$data['addId'].'"  '.$data['addHtml'].' /><i class="Validform_checktip">'.$data['tipMsg'].'</i></li>';
    return $str;
}
//密码框
function inputPassword($data)
{
    if($data['isMust']!=false) $data['isMustStr'] = '<b>*</b>'; else $data['isMustStr']='';
    $str = '<li style="'.$data['li_style'].'" class="'.$data['li_class'].'"><label>'.$data['title'].$data['isMustStr'].'</label><input name="'.$data['inputName'].'" value="'.$data['value'].'" type="password"  class="dfinput '.$data['addClass'].'" id="'.$data['addId'].'"  '.$data['addHtml'].' /><i class="Validform_checktip">'.$data['tipMsg'].'</i></li>';
    return $str;
}
//隐藏表单框
function hideInput($data)
{
    $str = '<input name="'.$data['inputName'].'" value="'.$data['value'].'" type="hidden" class="dfinput '.$data['addClass'].'" id="'.$data['addId'].'"  '.$data['addHtml'].' />';
    return $str;
}
//日期框Y-m-d H:i:s
function laydate($data)
{
    if($data['isMust']!=false) $data['isMustStr'] = '<b>*</b>'; else $data['isMustStr']='';
    $str = '<li style="'.$data['li_style'].'" class="'.$data['li_class'].'"><label>'.$data['title'].$data['isMustStr'].'</label><input name="'.$data['inputName'].'" value="'.$data['value'].'" type="text"  onclick="laydate({istime: true, format: \'YYYY-MM-DD hh:mm:ss\'})" tip＝"'.$data['tip'].'" class="dfinput '.$data['addClass'].'" id="'.$data['addId'].'"  '.$data['addHtml'].' /><i class="Validform_checktip">'.$data['tipMsg'].'</i></li>';
    return $str;
}

//日期框Y-m-d
function laydate2($data)
{
    if($data['isMust']!=false) $data['isMustStr'] = '<b>*</b>'; else $data['isMustStr']='';
    $str = '<li style="'.$data['li_style'].'" class="'.$data['li_class'].'"><label>'.$data['title'].$data['isMustStr'].'</label><input name="'.$data['inputName'].'" value="'.$data['value'].'" type="text"  onclick="laydate({istime: true, format: \'YYYY-MM-DD\'})" tip＝"'.$data['tip'].'" class="dfinput '.$data['addClass'].'" id="'.$data['addId'].'"  '.$data['addHtml'].' /><i class="Validform_checktip">'.$data['tipMsg'].'</i></li>';
    return $str;
}

//radio单选框
function radio($data)
{
    if($data['isMust']!=false) $data['isMustStr'] = '<b>*</b>'; else $data['isMustStr']='';
    $str = '<li style="'.$data['li_style'].'" class="'.$data['li_class'].'"><label>'.$data['title'].$data['isMustStr'].'</label><cite>';
    $i = 0;
    foreach($data['paramArr'] as $k=>$v){
        $i ++ ;
        if($i==1) $addHTml = $data['addHtml'];
        $yesStr = null;
        if($data['value']==$k){
            $yesStr = 'checked="checked" ';
        }
        $str .= '<input name="'.$data['inputName'].'" '.$tmpStr.' type="radio" value="'.$k.'" '.$yesStr.$addHTml.'  />&nbsp;&nbsp;'.$v.'&nbsp;&nbsp;&nbsp;&nbsp;';
    }
    $str .= '<i class="Validform_checktip">'.$data['tipMsg'].'</i></cite></li>';
    return $str;
}

//textarea
function textarea($data)
{
    if($data['isMust']!=false) $data['isMustStr'] = '<b>*</b>'; else $data['isMustStr']='';
    $str = '<li style="'.$data['li_style'].'" class="'.$data['li_class'].'"><label>'.$data['title'].$data['isMustStr'].'</label><textarea name="'.$data['inputName'].'"  class="textinput '.$data['addClass'].'" id="'.$data['addId'].'"  '.$data['addHtml'].'>'.$data['value'].'</textarea><i class="Validform_checktip">'.$data['tipMsg'].'</i></li>';
    return $str;
}


//编辑器
function editor($data)
{
    if($data['isMust']!=false) $data['isMustStr'] = '<b>*</b>'; else $data['isMustStr']='';
    $str = '<li><label>'.$data['title'].$data['isMustStr'].'</label><textarea name="'.$data['inputName'].'" style="width:800px;height:400px;visibility:hidden;" class="'.$data['addClass'].'" id="'.$data['addId'].'"  '.$data['addHtml'].'>'.$data['value'].'</textarea></li>';
    return $str;
}
//上传
function upload($data)
{
    if($data['isMust']!=false) $data['isMustStr'] = '<b>*</b>'; else $data['isMustStr']='';
    $str = '<li style="'.$data['li_style'].'" class="'.$data['li_class'].'"><label>'.$data['title'].$data['isMustStr'].'</label><input name="'.$data['inputName'].'" type="text" id="'.$data['addId'].'" class="dfinput '.$data['addClass'].'"  value="'.$data['value'].'" /> <input name="" type="button" id="'.$data['inputName'].'but" class="btn" value="上传图片"/><i class="Validform_checktip">'.$data['tipMsg'].'</i></li>';
    return $str;
}
//下啦菜单
function select($data)
{
    if($data['isMust']!=false) $data['isMustStr'] = '<b>*</b>'; else $data['isMustStr']='';
    foreach($data['paramArr'] as $k=>$v){
        if(count($v) == 1){
            $param[$k]['id']=$k;
            $param[$k]['name']=$v;
        }
    }
    if($param){
        $data['paramArr'] = null;
        $data['paramArr'] = $param;
    }
    foreach($data['paramArr'] as $k=>$v)
    {
        $selected = ($v['id']==$data['value'])?"selected":"";
        $optionStr .= '<option value="'.$v['id'].'" '.$selected.' >'.$v['name'].'</option>';
        if(is_array($v['childs'])||!empty($v['childs'])) $optionStr .=   selectChild($v['childs'],$data['value']);
        unset($selected);
    }
    $startOption = '<option value=""  >请选择</option>';
    $str =  '<li style="'.$data['li_style'].'" class="'.$data['li_class'].'"><label>'.$data['title'].$data['isMustStr'].'</label>
                <div class="vocation Validform_error">
                    <select class="select1 '.$data['addClass'].'" name="'.$data['inputName'].'"   '.$data['addHtml'].' >
                        '.$startOption.$optionStr.'
                    </select><i class="Validform_checktip">'.$data['tipMsg'].'</i>
                </div>
            </li>';
    return $str;
}

//下啦菜单无限循环
function selectChild($childs,$value='',$i='1')
{
    if(!is_array($childs)||empty($childs)) return '';
    for($a=0;$a<$i;$a++)
    {
        $str .= '&nbsp;&nbsp;&nbsp;&nbsp;';
    }
    $str .= "├─ ";
    $i++;
    foreach($childs as $k=>$v)
    {
        $selected = ($v['id']==$value)?"selected":"";
        $optionStr .= '<option value="'.$v['id'].'" '.$selected.' >'.$str.$v['name'].'</option>';
        if(is_array($v['childs'])||!empty($v['childs'])) $optionStr .=   selectChild($v['childs'],$value, $i);
        unset($selected);
    }
    return $optionStr;
}
//联动下拉菜单
function ldselect($data)
{
    if($data['isMust']!=false) $data['isMustStr'] = '<b>*</b>'; else $data['isMustStr']='';
    
    if(intval($data['value'])>0)
    {
        $positionStr = positionLd($data,$data['form_value'],$data['value']);
        $str =  '<li style="'.$data['li_style'].'" class="'.$data['li_class'].'"><label>'.$data['title'].$data['isMustStr'].'</label>
                <div class="vocation Validform_error ldselect" >'.$positionStr.'
                    <i class="Validform_checktip">'.$data['tipMsg'].'</i>
                </div>
            </li>';
        return $str;
    }
    
    foreach($data['paramArr'] as $k=>$v)
    {
        $selected = ($v['id']==$data['value'])?"selected":"";
        $optionStr .= '<option value="'.$v['id'].'" '.$selected.' >'.$v['name'].'</option>';
        if(is_array($v['childs'])||!empty($v['childs'])) $optionStr .=   selectChild($v['childs'],$data['value']);
        unset($selected);
    }
    
    $startOption = '<option value=""  >请选择</option>';
    $str =  '<li><label>'.$data['title'].$data['isMustStr'].'</label>
                <div class="vocation Validform_error ldselect" >
                    <select class="select1 ldselchild'.$data['form_value'].' '.$data['addClass'].'" style="width:100px;"  name="'.$data['inputName'].'" level=0   '.$data['addHtml'].' id="ld'.$data['form_value'].'" >
                        '.$startOption.$optionStr.'
                    </select><i class="Validform_checktip">'.$data['tipMsg'].'</i>
                </div>
            </li>';
    return $str;
}
//定位联动菜单
function positionLd($fieldInfo,$model,$value)
{
    
    $parent_id = M(ucfirst($model))->where('id='.$value)->getField('parent_id');
    if($parent_id=='') return '';
    $data = D('Model')->getSelAll($model,$parent_id,false);
    
    foreach($data as $k=>$v)
    {
        $selected = ($v['id']==$value)?"selected":"";
        $optionStr .= '<option value="'.$v['id'].'" '.$selected.' >'.$v['name'].'</option>';
        unset($selected);
    }
    $startOption = '<option value=""  >请选择</option>';
    $str =  '<select class="select1 ldselchild'.$fieldInfo['form_value'].' '.$fieldInfo['addClass'].'" style="width:100px;"  name="'.$fieldInfo['inputName'].'" level=0   '.$fieldInfo['addHtml'].' id="ld'.$fieldInfo['form_value'].'" >'.$startOption.$optionStr.'</select>';
    $parStr = positionLd($fieldInfo, $model, $parent_id);
    if($parStr) $str = $parStr.$str;
    return $str;
}
//复选框
function checkbox($data)
{
    if($data['isMust']!=false) $data['isMustStr'] = '<b>*</b>'; else $data['isMustStr']='';
    
    foreach($data['paramArr'] as $k=>$v)
    {
        $checked = (in_array($v['id'], $data['value']))?"checked":"";
        $checkboxStr .= '<li>&nbsp;<input class="'.$data['addClass'].'" style="width:20px;" value="'.$v['id'].'" name="'.$data['inputName'].'" type="checkbox" '.$checked.'>'.$v['name'];
        if(is_array($v['childs'])||!empty($v['childs'])) $checkboxStr .=   checkboxChild($v['childs'],$data);
        unset($checked);
    }
    $str =  '<li style="'.$data['li_style'].'" class="'.$data['li_class'].'"><label>'.$data['title'].$data['isMustStr'].'</label>
                <ul class="checkul" style="height:'.$data['height'].'">'.$checkboxStr.'</ul>
            </li>';
    return $str;
}

//复选框无限循环
function checkboxChild($childs,$data,$i='1')
{
    if(!is_array($childs)||empty($childs)) return '';
    for($a=0;$a<$i;$a++)
    {
        $str .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
    }
    $i++;
    
    foreach($childs as $k=>$v)
    {
        $checked = (in_array($v['id'], $data['value']))?"checked":"";
        $checkboxStr .= '<li>'.$str.'<input class="'.$data['addClass'].'" style="width:20px;" value="'.$v['id'].'" name="'.$data['inputName'].'" type="checkbox" '.$checked.'>'.$v["name"];
        if(is_array($v['childs'])||!empty($v['childs'])) $checkboxStr .=   checkboxChild($v['childs'],$data,$i);
        unset($checked);
    }
    $str =  '<ul>'.$checkboxStr.'</ul>';
    return $str;
}

//处理表单为可生成form元素数据的参数
function formField($fieldInfo,$value=array())
{
    if(empty($fieldInfo)) return false;
    $fieldType = C('FIELDTYPE');
    foreach ($fieldInfo as $k=>$v)
    {
        $formInput[$k]['inputType'] = $fieldType[$v['type']]['inputType'];
        $formInput[$k]['inputName'] = $v['field_name'];
        $formInput[$k]['title'] = $v['name'];
        $formInput[$k]['isMust'] = $v['must'];
        if($formInput[$k]['isMust']==1)
        {
            $validStr = $v['validform_type']?$v['validform_type'].',':'';
            $formInput[$k]['addHtml'] .= ' dataType="'.$validStr.'*" ';
        }
        else
        {
            $dataType = $v['validform_type']?' dataType="'.$v['validform_type'].'"':'';
            $formInput[$k]['addHtml'] .= $dataType.' ignore="ignore" ' ;
        }
        //处理ajaxurl验证唯一性  如果要想进行特殊的验证  请选择 否 然后在html里面对应增加方法
        if($v['ajax_url']==1)
        {
            $formInput[$k]['addHtml'] .= ' ajaxurl="'.U('/Admin/Model/checkFieldOnly/modelId/'.$v['model_id'].'/id/'.$value['id']).'" ';
        }
        $formInput[$k]['tipMsg'] = $v['tip_msg'];
        $formInput[$k]['addHtml'] .= $v['form_html'];
        $formInput[$k]['addClass'] = $v['form_class'];
        $formInput[$k]['addId'] = $v['form_id'];
        
        if(empty($value)) //如果是添加操作 首先看url参数 然后再看表单参数
        {
            if(trim($_GET[$v['field_name']])) $formInput[$k]['value'] = trim($_GET[$v['field_name']]);
            else $formInput[$k]['value'] = $v['form_value'];
        }
        else
        {
            $formInput[$k]['value'] = $value[$v['field_name']];
        }
        
        if($v['type']=='radio'||$v['type']=='select')
        {
            
            if(!empty($v['form_value']))
            {
                $exp = explode('}{', $v['form_value']);
                $fromValue = $exp['1'];
                $formInput[$k]['paramArr'] = getRadioValue($exp['0']);
            }
            if(empty($value))  //一般为添加数据
            {
                if(!empty($fromValue)) $formInput[$k]['value'] = $fromValue;  //如果表单有初始值 即为表单设置初始值
                else $formInput[$k]['value'] = '';
                if(trim($_GET[$v['field_name']]))
                {
                    $formInput[$k]['value'] = trim($_GET[$v['field_name']]);
                }
                else
                {
                    if(!empty($fromValue)) $formInput[$k]['value'] = $fromValue;  //如果表单有初始值 即为表单设置初始值
                    else $formInput[$k]['value'] = '';
                }
            }
            else
            {
                $formInput[$k]['value'] = $value[$v['field_name']];
               
            }
            if($v['type']=='radio')
            {
                foreach($formInput[$k]['paramArr'] as $ke=>$va)
                {
                    $ret[$va['id']] = $va['name'];
                }
                unset($formInput[$k]['paramArr']);
                $formInput[$k]['paramArr'] = $ret;
            }
        }
        //如果是联动模型的
        if($v['type']=='ldselect')
        {
            $formInput[$k]['paramArr'] = D('Model')->getSelAll($v['form_value'],0,false);
            $formInput[$k]['form_value'] = $v['form_value'];
            if(empty($value)) $formInput[$k]['value'] = '';
            else  $formInput[$k]['value'] = $value[$v['field_name']];
        }
    }
    return $formInput;
}
//联动字段处理方法


//解析radio select等字段的值
function getRadioValue($fieldValue,$value='')
{
    
    if(empty($fieldValue)) return '';
    //首先解析是方法的参数
    if(strpos($fieldValue,'fun__')===0)
    {
        $fun = substr($fieldValue,5);
        $result = $fun;
        return $result;
    }
    //模型的方法
    if(strpos($fieldValue,'model__')===0)
    {
        $fun = substr($fieldValue,7);
        $arr = explode('_',$fun);
       
        if(empty($arr['1'])) //如果不存在 视为ModelModel里面的方法  这个以后可能还得再加强灵活性
        {
            $modelFun = explode('-', $arr['0']);
            
            $result = D('Model')->$modelFun['0']($modelFun['1']);
        }
        else
        {
            //$result = D(ucfirst($arr['0']))->$arr['1'];
        }
        return $result;
    }
    
    //模型的方法
    if(strpos($fieldValue,'action__')===0)
    {
        
        $fun = substr($fieldValue,8);
        $arr = explode('_',$fun);
       
        if(empty($arr['1'])) //如果不存在 视为ModelModel里面的方法  这个以后可能还得再加强灵活性
        {
            
            $modelFun = explode('-', $arr['0']);
            import("@.Action.ModelAction");
            $action = new ModelAction();
            
            $result = $action->$modelFun['0']($modelFun['1']);
           
        }
        else
        {
            $modelFun = explode('-', $arr['1']);
            
            $actionAction = $arr['0'].'Action';
            import("@.Action.".$actionAction);
            $action = new $actionAction();
            
            $result = $action->$modelFun['0']($modelFun['1']);
            
            //$result = D(ucfirst($arr['0']))->$arr['1'];
        }
        return $result;
    }
    
    $i = 0;
    
    $arr = explode(',', $fieldValue);
    if(is_array($arr)&&!empty($arr))
    {
        foreach($arr as $key=>$val)
        {
            $i++;
            $arr2 = explode('|',$val);
            $result[$i] = array('id'=>$arr2['0'],'name'=>$arr2['1']);
            if(!empty($value)&&$arr2['0']==$value)
            {
                return $arr2['1'];
                break;
            }
        }
    }
    else
    {
        return '';
    }
    
    return $result;
    
}
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
            $result[$k]['childs'] = sortChilds($dataArr , $v['id']);
        }
    }
    return $result;
}
?>