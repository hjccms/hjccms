<?php
//定义项目名称
/*
 * 求 b 相对于a的路径  (在a文件里求b的路径)
 */
$a = '/a/b/c/d/e.php';
$b = '/a/b/12/d/34/c.php';

function getPath($a,$b)
{
    $arrA = explode('/',$a);
    $arrB = explode('/',$b);
    print_r($arrA);
    print_r($arrB);
    $ai = 0;
    //求出A相对于B 除去开始相同的部分 后面不同的部分
    foreach($arrA as $k=>$v)
    {
        if($v==$arrB[$k]&&$ai==0)            continue;
        $newArrA[$k] = $v;
        $ai ++ ;
    }
    $bi =0;
    //求出B相对于A 除去开始相同的部分 后面不同的部分
    foreach($arrB as $k=>$v)
    {
        if($v==$arrA[$k]&&$bi==0)            continue;
        $newArrB[$k] = $v;
        $bi ++ ;
    }
    //删除最后文件元素
    array_pop($newArrA);
    foreach($newArrA as $v)
    {
        $str .= '../'; //组合成字符串
    }
    $str2 = implode('/', $newArrB);  
    return $str.$str2;
}


?>