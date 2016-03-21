<?php
/*
 * 管理菜单模型
 */
class FileinfoModel extends Model 
{
    //自动完成
    protected $_auto = array( array('create_time','time',1,'function'));
    //自动映射
    protected $_map = array(
        'type'    => 'mime',
    );
    
}
