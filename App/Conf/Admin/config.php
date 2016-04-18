<?php
return array(
	//'配置项'=>'配置值'
    'DB_PREFIX'             => 'sys_',    // 数据库表前缀
    
    //字段类型
    'FIELDTYPE' => array(
        'tinyint'=>array('id'=>'tinyint','fieldType'=>'tinyint','fieldDefault'=>'0','name'=>'小整型','longth'=>'1','inputType'=>'input'),
        'int'=>array('id'=>'int','fieldType'=>'int','name'=>'整型','fieldDefault'=>'0','longth'=>'1','inputType'=>'input'),
        'bigint'=>array('id'=>'bigint','fieldType'=>'bigint','fieldDefault'=>'0','name'=>'超大整型','longth'=>'1','inputType'=>'input'),
        'float'=>array('id'=>'float','fieldType'=>'float','fieldDefault'=>'0','name'=>'小数型','inputType'=>'input'),
        'varchar'=>array('id'=>'varchar','fieldType'=>'varchar','name'=>'单行文本','longth'=>'1','inputType'=>'input'),
        'morechar'=>array('id'=>'morechar','fieldType'=>'varchar','name'=>'多行文本','longth'=>'1','inputType'=>'textarea'),
        'hiddenchar'=>array('id'=>'hiddenchar','fieldType'=>'varchar','name'=>'隐藏表单','longth'=>'1','inputType'=>'hideInput'),
        'text'=>array('id'=>'text','fieldType'=>'text','name'=>'编辑器','longth'=>'1','inputType'=>'editor'),
        'radio'=>array('id'=>'radio','fieldType'=>'tinyint','name'=>'单选按钮','longth'=>'5','inputType'=>'radio'),
        'uploadimg'=>array('id'=>'uploadimg','fieldType'=>'varchar','name'=>'图片上传','longth'=>'250','inputType'=>'upload'),
        'select'=>array('id'=>'select','fieldType'=>'tinyint','fieldDefault'=>'0','name'=>'下拉选择','longth'=>'250','inputType'=>'select'),
        'laydata'=>array('id'=>'laydata','fieldType'=>'datetime','fieldDefault'=>'','name'=>'日期表单','longth'=>'0','inputType'=>'laydate'),
        'ldselect'=>array('id'=>'ldselect','fieldType'=>'tinyint','fieldDefault'=>'0','name'=>'联动下拉','longth'=>'250','inputType'=>'ldselect'),
    ),    
    //验证规则
    'VALIDFORMTYPE' =>array(
        array('id'=>'n','name'=>'数字类型'),
        array('id'=>'s','name'=>'字符串类型'),
        array('id'=>'m','name'=>'手机号码格式'),
        array('id'=>'e','name'=>'EMAIL格式'),
        array('id'=>'url','name'=>'网址格式'),
    ),
    //模型类型
    'MODELFUNTYPE' => array(
        '1' => '功能模型',
        '2' => '内容模型',
        '3' => '联动模型',
    ),
    //链接参数
    'MENUPARAM' =>array('id','pid','modelId','importAction','importFun','fromUrl'),
    
    //函数参数
    'MENUFUNCPARAM' =>array('id','pid','modelId','importAction','importFun','fromUrl'),
);
?>