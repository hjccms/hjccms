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
/**
 * 获取当前页面完整URL地址
 */
function get_url() {
	$sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
	$php_self = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
	$path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
	$relate_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $php_self . (isset($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : $path_info);
	return $sys_protocal . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '') . $relate_url;
}

function get_hosturl() {
	$sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
	$php_self = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
	$path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
	$relate_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $php_self . (isset($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : $path_info);
	return $sys_protocal . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '');
}

//处理字符串左边的特殊字符 目前只支持左边
function atrim($str,$condition)
{
    if(is_array($condition)&&$condition)
    {
        foreach($condition as $k=>$v)
        {
            if(stripos($str, $v)===0)
            {
                $str = substr($str, strlen($v));
            }
        }
    }
    else
    {
       if($condition)
       {
            $str = substr($str, strlen($condition)-1);
       }
    }
    $str = trim($str);
    return $str;
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

//浏览文件夹里所有的文件
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
        '6' => '有效调卷外呼',
        '100' => '其他方式'
	);
	if (isset($flag)) {
		return $arr[$flag];
	} else {
		return $arr;
	}
}

function get_level($flag = null) {
	$arr = array(
		'1' => 'Level 1',
		'2' => 'Level 2',
        '3' => 'Level 3',
        '4' => 'Level 4',
        '5' => 'Level 5',
        '6' => 'Level 6'
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


function get_student_status($flag = null) {
	$arr = array(
		'1' => '报名',
		'2' => '咨询',
	);
	if (isset($flag)) {
		return $arr[$flag];
	} else {
		return $arr;
	}
}

function get_listening_answer($flag = null){
    $arr = array(
        1=>array('answer'=>2,'score'=>1),
        2=>array('answer'=>3,'score'=>1),
        3=>array('answer'=>1,'score'=>1),
        4=>array('answer'=>3,'score'=>1),
        5=>array('answer'=>3,'score'=>1),
        6=>array('answer'=>3,'score'=>1),
        7=>array('answer'=>2,'score'=>1),
        8=>array('answer'=>2,'score'=>1),
        9=>array('answer'=>1,'score'=>1),
        10=>array('answer'=>3,'score'=>1),
        11=>array('answer'=>1,'score'=>2),
        12=>array('answer'=>1,'score'=>2),
        13=>array('answer'=>2,'score'=>2),
        14=>array('answer'=>2,'score'=>2),
        15=>array('answer'=>2,'score'=>2),
        16=>array('answer'=>3,'score'=>2),
        17=>array('answer'=>2,'score'=>2),
        18=>array('answer'=>3,'score'=>2),
        19=>array('answer'=>3,'score'=>2,),
        20=>array('answer'=>2,'score'=>2),
        21=>array('answer'=>1,'score'=>4),
        22=>array('answer'=>2,'score'=>4),
        23=>array('answer'=>1,'score'=>4),
        24=>array('answer'=>1,'score'=>4),
        25=>array('answer'=>3,'score'=>4)
    );
    if (isset($flag)) {
		return $arr[$flag];
	} else {
		return $arr;
	}
}

function get_listening_level($total) {
	if ($total >= 0 && $total <= 20){
		$re = 1;
    }elseif ($total >= 21 && $total <= 35){
		$re = 2;
    }elseif ($total >= 36 && $total <= 50){
		$re = 3;
    }else{
        $re = 0;
    }
	return $re;
}

function get_listening_level_text($flag = null) {
	$arr = array(
		'1' => array(
            'level'=>'1-2级',
            'rank'=>'初级',
            'title'=>'你的英文水平还不错哦！',
            'text'=>'你能听懂、认读入门级单词和短语，但是词汇量有限。从基础课程开始，现在就和奥尼少儿英语的老师们一起说英语吧！',
            'advise'=>'学习建议：你需要积累更多的单词量和简单句型，多和外教老师进行听说练习，模仿外教发音，从基础开始努力。',
        ),
		'2' => array(
            'level'=>'3-4级',
            'rank'=>'中级',
            'title'=>'你的英文水平相当不错！',
            'text'=>'你有简单的日常单词量基础和英语听力能力，能够和外教老师进行基础英语对话，想要发音正宗、练习更多口语主题对话，还要继续努力学习哦！',
            'advise'=>'学习建议：你需要学习不同话题的表达，不仅积累更多的单词，更要练习多用句子进行问答，并纠正发音。',
        ),
        '3' => array(
            'level'=>'5-6级',
            'rank'=>'高级',
            'title'=>'你的英文水平非常棒哦！',
            'text'=>'你有一定的英语基础，能够就日常生活展开简单对话、需要更多口语练习建立英文思维，流畅使用不同句型完整表达自己的想法，相信你会越来越棒哒！',
            'advise'=>'学习建议：你需要更多地模仿外教老师关于不同话题的表达方式，学习地道的英语表达，多张口练习，提高英语表达的流畅度。',
        ),
	);
	if (isset($flag)) {
		return $arr[$flag];
	} else {
		return $arr;
	}
}

function curlPost($url, $postdata) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postdata, "", "&"));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, "wx curl post");
	$data = curl_exec($ch);
	curl_close($ch);
	return $data;
}

function curlGet($url) {
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_BINARYTRANSFER, true); 
	$output = curl_exec($ch);
	curl_close($ch);
	return $output;
}

function actionPost($url, $data){
    $ch = curl_init();
    $header = "Accept-Charset: utf-8";
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $tmpInfo = curl_exec($ch);
    if (curl_errno($curl)) {
        echo curl_error($curl);
    }
    curl_close($curl);
    return $tmpInfo;
}


function get_rand_char($length){
    $str = null;
    $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
    $max = strlen($strPol)-1;
    for($i=0;$i<$length;$i++){
     $str.=$strPol[rand(0,$max)];
    }
    return $str;
}
    
//记录日志
function writeLog($txt,$content)
{
    $fp = fopen($txt,"a");
    flock($fp, LOCK_EX) ;
    fwrite($fp,"执行日期：".strftime("%Y%m%d%H%M%S",time())." 记录反馈：".$content."\r\n");
    flock($fp, LOCK_UN);
    fclose($fp);
}

?>