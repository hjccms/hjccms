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

//隐藏表单框
function hideInput($data)
{
    $str = '<input name="'.$data['inputName'].'" value="'.$data['value'].'" type="hidden" class="dfinput '.$data['addClass'].'" id="'.$data['addId'].'"  '.$data['addHtml'].' />';
    return $str;
}

//radio单选框
function radio($data)
{
    if($data['value']=='1') $yesStr = 'checked="checked"';
    else $noStr = 'checked="checked"';
    $str = '<li><label>'.$data['title'].'</label><cite>';
    foreach($data['paramArr'] as $k=>$v){
        $str .= '<input name="'.$data['inputName'].'" type="radio" value="'.$v['id'].'" '.$yesStr.'  />&nbsp;&nbsp;'.$v['name'].'&nbsp;&nbsp;&nbsp;&nbsp;';
    }
    $str .= '</cite></li>';
    return $str;
}

//textarea
function textarea($data)
{
    $str = '<li><label>'.$data['title'].'</label><textarea name="'.$data['inputName'].'" style="width:'.$data['width'].'px;height:'.$data['height'].'px;" class="dfinput '.$data['addClass'].'" id="'.$data['addId'].'"  '.$data['addHtml'].'>'.$data['value'].'</textarea></li>';
    return $str;
}


//编辑器
function editor($data)
{
    $str = '<li><label>'.$data['title'].'</label><textarea name="'.$data['inputName'].'" style="width:800px;height:400px;visibility:hidden;" class="'.$data['addClass'].'" id="'.$data['addId'].'"  '.$data['addHtml'].'>'.$data['value'].'</textarea></li>';
    return $str;
}
//上传
function upload($data)
{
    $str = '<li><label>'.$data['title'].'</label><input name="'.$data['inputName'].'" type="text" id="'.$data['addId'].'" class="dfinput '.$data['addClass'].'"  value="'.$data['value'].'" /> <input name="" type="button" id="'.$data['uploadId'].'" class="btn" value="上传图片"/></li>';
    return $str;
}
//下啦菜单
function select($data)
{
    if($data['isMust']!=false) $data['isMustStr'] = '<b>*</b>'; else $data['isMustStr']='';
    
    foreach($data['paramArr'] as $k=>$v)
    {
        $selected = ($v['id']==$value)?"selected":"";
        $optionStr .= '<option value="'.$v['id'].'" '.$selected.' >'.$v['name'].'</option>';
        if(is_array($v['childs'])||!empty($v['childs'])) $optionStr .=   selectChild($v['childs'],$data['value']);
        unset($selected);
    }
    $startOption = empty($data['value'])?'<option value="" selected >请选择</option>':'';
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

//textare  多行文本框
function textare($data)
{
    $str = '<li><label>'.$data['title'].'</label><textarea name="'.$data['inputName'].'" type="text" id="'.$data['addId'].'" class="dfinput '.$data['addClass'].'"   >'.$data['value'].'</textarea></li>';
    return $str;
}