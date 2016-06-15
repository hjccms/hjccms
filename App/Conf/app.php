<?php

return array(
    'APP_KEY' => 'dj_wia$84k&lp',  //项目加密key值
    
    'SITEURL' => 'http://an.haowj.com',
    
    'WX_URL' => 'http://wx.haowj.com',
    //登录注册接口地址
    'LOGINAPIURL' => 'http://student.haowj.com/Home/Api/ajaxCheckUser',
    'REGISTERAPIURL' => 'http://student.haowj.com/Home/Api/ajaxRegUser',
    //表单hash验证开启
    'TOKEN_ON'=>true,  // 是否开启令牌验证
    'TOKEN_NAME'=>'__hash__',    // 令牌验证的表单隐藏字段名称
    'TOKEN_TYPE'=>'md5',  //令牌哈希验证规则 默认为MD5
    'TOKEN_RESET'=>true,  //令牌验证出错后是否重置令牌 默认为true
    'DEFAULT_FILTER'=>'htmlspecialchars,strip_tags', //get和post 过滤方法
    //有效和无效
    
    'APPVALIDTRUE' => '1',
    'APPVALIDFALSE'=> '0',
    
    //附件设置
    'PUBLICURL' => 'http://an.haowj.com',  //项目静态文件地址配置
    'UPLOADFILEDIR' => '/Public/Upload/',//上传文件保存地址
    'FILEMAXSIZE' => 3145728 ,//最大允许附件上传大小
    'IMAGETHUMB' => false,//是否开启缩略图   上传url参数imagethumb控制作为最高优先级
    'THUMBPREFIX' => 'thumb_', //缩略图前缀
    'UPLOADFILETYPE' => array(
                'image' => array('gif', 'jpg', 'jpeg', 'png', 'bmp'),
                'flash' => array('swf', 'flv'),
                'media' => array('swf', 'flv', 'mp3', 'wav', 'wma', 'wmv', 'mid', 'avi', 'mpg', 'asf', 'rm', 'rmvb'),
                'file' => array('doc', 'docx', 'xls', 'xlsx', 'ppt', 'htm', 'html', 'txt', 'zip', 'rar', 'gz', 'bz2'),
        ),   //系统所允许上传文件的类型
    
)
?>

