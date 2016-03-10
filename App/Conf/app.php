<?php

return array(
    'APP_KEY' => 'dj_wia$84k&lp',  //项目加密key值
    'APP_PUBLIC' => 'http://hjc.an.haowj.com/Public/',  //项目静态文件地址配置
    //表单hash验证开启
    'TOKEN_ON'=>true,  // 是否开启令牌验证
    'TOKEN_NAME'=>'__hash__',    // 令牌验证的表单隐藏字段名称
    'TOKEN_TYPE'=>'md5',  //令牌哈希验证规则 默认为MD5
    'TOKEN_RESET'=>true,  //令牌验证出错后是否重置令牌 默认为true
)
?>

