<?php
/*
 * 管理菜单模型
 */
class ModelModel extends Model 
{
    //自动完成
    protected $_auto = array( array('create_time','time',1,'function') );
    //站点信息  支持全部和单个
    function getModel($modelId='')
    {
        if($modelId&&  intval($modelId)>0) $condition = array('id'=>$modelId);
        $ret = $this->where($condition)->order("id asc")->select();
        if(!empty($modelId))            return $ret['0'];  //如果查询单个信息直接返回 数组
        else return $ret;
    }
    
    //添加和更新菜单
    function addModel($data)
    {
        $hash = $data['__hash__'];
        if(!$this->create($data))
        {
            $msg = $this->getError();
            return $msg;
        }
        if($data['id']>0)
        {
            $this->save();
            $id = $data['id'];
        }
        else
        {
            if($this->createTable($data['table_name'])) $id = $this->add();
            else return false;
        }
        return $id; 
       
    }
    
    //创建数据库
    function createTable($tableName)
    {
        $sql = 'CREATE TABLE `'.C('DB_PREFIX').$tableName.'` ( `id`  int(11) NOT NULL AUTO_INCREMENT ,`create_time`  int(11) NOT NULL ,`valid`  tinyint(2) NOT NULL DEFAULT 1 ,`del`  tinyint(2) NULL DEFAULT NULL ,PRIMARY KEY (`id`),INDEX `id` (`id`) ) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 DELAY_KEY_WRITE=0 ';
        if(mysql_query($sql)) return true;
        else return false;
        
    }
    function checkField($filed,$param,$con=array())
    {
        $condition = array($filed=>$param);
        $condition = array_merge($condition,$con);
        $ret = $this->where($condition)->getField('id');
        if($ret) return true; else return false;
    }
    
    
}
