<?php

function show_db_errorxx() {
    exit('系统访问量大，请稍等添加数据');
}
//加密解密方法 $operation  E为加密 D为解密
function encrypt($string, $operation, $key = '') 
{
    if($key=='') $key = C('APP_KEY');
    $key = md5($key);
    $key_length = strlen($key);
    if($operation=='D')
    {
        $string = str_replace ('-hjc-', '+', $string);
        $string = str_replace ('_hjc_', '/', $string);
    }
    $string = $operation == 'D' ? base64_decode($string) : substr(md5($string . $key), 0, 8) . $string;
    $string_length = strlen($string);
    $rndkey = $box = array();
    $result = '';
    for ($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($key[$i % $key_length]);
        $box[$i] = $i;
    }
    for ($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }
    for ($a = $j = $i = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        $result.=chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }
    if ($operation == 'D') {
        if (substr($result, 0, 8) == substr(md5(substr($result, 8) . $key), 0, 8)) {
            return substr($result, 8);
        } else {
            return'';
        }
    } else {
        $ret = str_replace('=', '', base64_encode($result));
        $ret = str_replace('+', '-hjc-', $ret);
        $ret = str_replace('/', '_hjc_', $ret);
        return $ret;
    }
    
}

function get_valid($flag = null) {
	$arr = array(
		'0' => '否',
		'1' => '是',
	);
	if (isset($flag)) {
		return $arr[$flag];
	} else {
		return $arr;
	}
}

function get_gender($flag = null) {
	$arr = array(
		'1' => '男',
		'2' => '女',
	);
	if (isset($flag)) {
		return $arr[$flag];
	} else {
		return $arr;
	}
}

//获取菜单类型
function get_menu_type($flag = null) {
	$arr = array(
		'1' => '链接',
		'2' => '内容按钮',
        '3' => '列表按钮',
	);
	if (isset($flag)) {
		return $arr[$flag];
	} else {
		return $arr;
	}
}


//菜单参数拼接
function get_param(){
    $param = null;
    $menuParam = C('MENUPARAM');
    foreach($menuParam as $v){
        $val = null;
        $val = $_GET[$v];
        if($val){
            $param[$v] = $val;
        }
    }
    //根据键的升序对数据进行排序
    ksort($param);
    $param = http_build_query($param);
    return $param;
}
//对URL 参数进行排序处理
function httpBuildQuery($str)
{
    $newArr = urlStrToArr($str);
    $str = urldecode(http_build_query($newArr)); //重新组合成已经排序过的字符串
    return $str;
}
//URl参数 转变成数组
function urlStrToArr($str)
{
    $str = urldecode($str); //实体化URL参数  避免有些编码的url解析错误
    if(empty($str)) return '';
    $arr = explode('&',$str);
    foreach($arr as $key=>$val)
    {
        $arrZi = explode('=',$val,2);
        $newArr[$arrZi['0']] = $arrZi['1'];
    }
    ksort($newArr);
    return $newArr;
}
//获取列表按钮
function get_list_button($listButton,$arr){
    echo D('Menu')->getListButton($listButton,$arr);
}

//获取列表按钮
function get_content_button($contentButton,$arr){
    echo D('Menu')->getContentButton($contentButton,$arr);
}

//获取角色类型
function get_role_type($flag = null,$str=false,$type=null) {
    if($type<3){
        if($str == 1){
            $arr[1] = "创始人";
        }
        $arr[2] = "功能管理员";
    }
    $arr[3] = "权限管理员";
	if (isset($flag)) {
		return $arr[$flag];
	} else {
		return $arr;
	}
}

//浏览文件夹
function readFiles($dir)
{
    $handler = opendir($dir);  
    while (($filename = readdir($handler)) !== false) 
    {
        if ($filename != "." && $filename != "..") 
        {  
            $files[] = $filename;
        }  
       
    }  
    closedir($handler);  
    return $files;
}


function get_channel($flag = null) {
	$arr = array(
		'1' => '派发单页',
		'2' => '直接上门',
        '3' => 'LED显示屏/招牌',
        '4' => '家属、朋友推荐',
        '5' => '在读学员推荐',
        '100' => '其他方式'
	);
	if (isset($flag)) {
		return $arr[$flag];
	} else {
		return $arr;
	}
}

function get_record_type($flag = null) {
	$arr = array(
		'1' => '纪录',
		'2' => '回访',
	);
	if (isset($flag)) {
		return $arr[$flag];
	} else {
		return $arr;
	}
}

function get_record_status1($flag = null) {
	$arr = array(
		'1' => '电话咨询',
		'2' => '电话上门咨询',
        '3' => '直接上门咨询',
        '4' => '再次上门',
        '5' => '首次上门报名',
        '6' => '再次上门报名',
        '100' => '其他',
	);
	if (isset($flag)) {
		return $arr[$flag];
	} else {
		return $arr;
	}
}

function get_record_status2($flag = null) {
	$arr = array(
		'1' => '观望',
		'2' => '预约试用',
		'3' => '预约联系',
		'4' => '直接付定金',
		'5' => '直接下单',
		'15' => '长期跟踪',
		'6' => '无购买能力',
		'7' => '以为免费',
		'8' => '非本人注册',
		'9' => '直接拒绝',
		'10' => '无人接听',
		'11' => '占线',
		'12' => '关机',
		'13' => '拒接',
		'14'=>'交易完成',
		'100' => '其他',
	);
	if (isset($flag)) {
		return $arr[$flag];
	} else {
		return $arr;
	}
}

?>