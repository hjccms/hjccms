<?php
return array(
	//'配置项'=>'配置值'
    'SHOW_PAGE_TRACE'=>true,
    'URL_HTML_SUFFIX'=>'.html',
    //分组配置
    'APP_GROUP_LIST' => 'Home,Admin', //分组配置
    'DEFAULT_GROUP'     => 'Home', //默认分组
    'DEFAULT_MODULE'     => 'Index', //默认分组
    'DEFAULT_ACTION'     => 'index', //默认分组
    'SESSION_AUTO_START' => true, //是否开启session
    'URL_MODEL' => '2',
    'LOAD_EXT_CONFIG' => 'app,db,debug',  //加载其他配置
    //其他配置
    //默认错误跳转对应的模板文件
    'TMPL_ACTION_ERROR' => 'App/Tpl/Admin/Index/error.html',
    //默认成功跳转对应的模板文件
    'TMPL_ACTION_SUCCESS' => 'App/Tpl/Admin/Index/success.html',
);
?>