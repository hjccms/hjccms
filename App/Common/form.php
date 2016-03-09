<?php

/*
 * 自定义表单函数库 
 */

//普通文本框
function input($data)
{
    if($data['isMust']!=false) $data['isMustStr'] = '<b>*</b>'; else $data['isMustStr']='';
    $str = '<li><label>'.$data['title'].$data['isMustStr'].'</label><input name="'.$data['inputName'].'" value="'.$data['value'].'" type="text" class="dfinput '.$data['addClass'].'" id="'.$data['addId'].'"  '.$data['addHtml'].' /><i class="Validform_checktip">'.$data['tipMsg'].'</i></li>';
    return $str;
}
//隐藏表单框
function hideInput($data)
{
    $str = '<input name="'.$data['inputName'].'" value="'.$data['value'].'" type="hidden" class="dfinput '.$data['addClass'].'" id="'.$data['addId'].'"  '.$data['addHtml'].' />';
    return $str;
}
//radio单选框 是和否
function radio($data)
{
    if($data['value']=='1') $yesStr = 'checked="checked"';
    else $noStr = 'checked="checked"';
    $str = '<li><label>'.$data['title'].'</label><cite><input name="'.$data['inputName'].'" type="radio" value="1" '.$yesStr.'  />是&nbsp;&nbsp;&nbsp;&nbsp;<input name="'.$data['inputName'].'" type="radio" value="0" '.$noStr.' />否</cite></li>';
    return $str;
}

