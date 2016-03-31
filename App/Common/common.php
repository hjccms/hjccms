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
        return str_replace('=', '', base64_encode($result));
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
            $param .= $v."=".$val."&";
        }
    }
    $param = trim($param,"&");
    return $param;
}

function get_list_button($listButton,$id){
    echo D('Menu')->getListButton($listButton,$id);
}

?>