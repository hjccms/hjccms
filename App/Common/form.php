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
 * @return string
 */
//普通文本框
function input($data)
{
    if($data['isMust']!=false) $data['isMustStr'] = '<b>*</b>'; else $data['isMustStr']='';
    $str = '<li><label>'.$data['title'].$data['isMustStr'].'</label><input name="'.$data['inputName'].'" value="'.$data['value'].'" type="text" tip＝"'.$data['tip'].'" class="dfinput '.$data['addClass'].'" id="'.$data['addId'].'"  '.$data['addHtml'].' /><i class="Validform_checktip">'.$data['tipMsg'].'</i></li>';
    return $str;
}
//密码框
function inputPassword($data)
{
    if($data['isMust']!=false) $data['isMustStr'] = '<b>*</b>'; else $data['isMustStr']='';
    $str = '<li><label>'.$data['title'].$data['isMustStr'].'</label><input name="'.$data['inputName'].'" value="'.$data['value'].'" type="password"  class="dfinput '.$data['addClass'].'" id="'.$data['addId'].'"  '.$data['addHtml'].' /><i class="Validform_checktip">'.$data['tipMsg'].'</i></li>';
    return $str;
}
//隐藏表单框
function hideInput($data)
{
    $str = '<input name="'.$data['inputName'].'" value="'.$data['value'].'" type="hidden" class="dfinput '.$data['addClass'].'" id="'.$data['addId'].'"  '.$data['addHtml'].' />';
    return $str;
}
//日期框
function laydate($data)
{
    if($data['isMust']!=false) $data['isMustStr'] = '<b>*</b>'; else $data['isMustStr']='';
    $str = '<li><label>'.$data['title'].$data['isMustStr'].'</label><input name="'.$data['inputName'].'" value="'.$data['value'].'" type="text"  onclick="laydate({istime: true, format: \'YYYY-MM-DD hh:mm:ss\'})" tip＝"'.$data['tip'].'" class="dfinput '.$data['addClass'].'" id="'.$data['addId'].'"  '.$data['addHtml'].' /><i class="Validform_checktip">'.$data['tipMsg'].'</i></li>';
    return $str;
}
//radio单选框
function radio($data)
{
    if($data['isMust']!=false) $data['isMustStr'] = '<b>*</b>'; else $data['isMustStr']='';
    $str = '<li><label>'.$data['title'].$data['isMustStr'].'</label><cite>';
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
    $str = '<li><label>'.$data['title'].$data['isMustStr'].'</label><textarea name="'.$data['inputName'].'"  class="textinput '.$data['addClass'].'" id="'.$data['addId'].'"  '.$data['addHtml'].'>'.$data['value'].'</textarea><i class="Validform_checktip">'.$data['tipMsg'].'</i></li>';
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
    $str = '<li><label>'.$data['title'].$data['isMustStr'].'</label><input name="'.$data['inputName'].'" type="text" id="'.$data['addId'].'" class="dfinput '.$data['addClass'].'"  value="'.$data['value'].'" /> <input name="" type="button" id="'.$data['uploadId'].'" class="btn" value="上传图片"/><i class="Validform_checktip">'.$data['tipMsg'].'</i></li>';
    return $str;
}
//下啦菜单
function select($data)
{
    if($data['isMust']!=false) $data['isMustStr'] = '<b>*</b>'; else $data['isMustStr']='';
    
    foreach($data['paramArr'] as $k=>$v)
    {
        $selected = ($v['id']==$data['value'])?"selected":"";
        $optionStr .= '<option value="'.$v['id'].'" '.$selected.' >'.$v['name'].'</option>';
        if(is_array($v['childs'])||!empty($v['childs'])) $optionStr .=   selectChild($v['childs'],$data['value']);
        unset($selected);
    }
    $startOption = '<option value=""  >请选择</option>';
    $str =  '<li><label>'.$data['title'].$data['isMustStr'].'</label>
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
    $str =  '<li><label>'.$data['title'].$data['isMustStr'].'</label>
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
        if(empty($value)) $formInput[$k]['value'] = $v['form_value'];
           else  $formInput[$k]['value'] = $value[$v['field_name']];
        if($v['type']=='radio'||$v['type']=='select')
        {

            if(!empty($v['form_value']))
            {
                $formInput[$k]['paramArr'] = getRadioValue($v['form_value']);
            }
            if(empty($value)) $formInput[$k]['value'] = '';
            else  $formInput[$k]['value'] = $value[$v['field_name']];
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
    }
    return $formInput;
}


//解析radio select等字段的值
function getRadioValue($fieldValue,$value='')
{
    
    if(empty($fieldValue)) return '';
    //首先解析是方法的参数
    if(strpos($fieldValue,'fun__')===0)
    {
        $fun = substr($fieldValue,4);
        $result = $fun;
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