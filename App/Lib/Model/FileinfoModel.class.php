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
    function changeFile($hash,$id,$table)
    {
        if(!$hash) return false;
        //先把以前的旧图片设为无效
        $this->where(array('valid_md5'=>$hash))->save(array('model_name'=>$table,'model_id'=>$id,'valid'=>1));
        return;
    }
}
