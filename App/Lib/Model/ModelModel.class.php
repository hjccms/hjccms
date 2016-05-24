<?php
/*
 * 管理菜单模型
 */
class ModelModel extends Model 
{
    //自动完成
    protected $_auto = array( array('create_time','time',1,'function') );
    //站点信息  支持全部和单个
    function getModel($modelId='',$con='')
    {
        if($modelId&&  intval($modelId)>0) $condition = array('id'=>$modelId);
        if(!empty($con)) $condition['_string'] = $con;
        $ret = $this->where($condition)->order("type asc,id asc")->select();
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
        $sql = 'CREATE TABLE `'.C('DB_PREFIX').$tableName.'` ( `id`  int(11) NOT NULL AUTO_INCREMENT ,`site_id`  int(11) NOT NULL  ,`admin_id`  int(11) NOT NULL  ,`create_time`  int(11) NOT NULL ,`valid`  tinyint(2) NOT NULL DEFAULT 1 ,`del`  tinyint(2) NULL DEFAULT NULL ,`sort`  int(11) NOT NULL DEFAULT 0 , PRIMARY KEY (`id`),INDEX `id` (`id`) ) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 DELAY_KEY_WRITE=0 ';
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
    
    //获取sel的数据方法
    function getSelAll($model,$parentId='0',$childs=true)
    {
        $adminInfo = Cookie('adminInfo');
        $condition['_string'] = "site_id=".$adminInfo->site_id." and  del is null";
        
        if($childs==false) $condition['_string'] .= " AND parent_id = '$parentId' ";
        //$con = D('Admin')->getSiteCondition($adminInfo->site_id,$adminInfo->id);
        //if($con) $condition = array_merge ($con,$condition);
        $selAll = M(ucfirst($model))->where($condition)->field('id,name,parent_id')->select();
        
        if($childs==true&&!empty($selAll))
        {
            $parentId = $parentId?$parentId:0;
            $result = $this->sortChilds($selAll, $parentId);
            return $result;
        }
        else 
        {
            return $selAll;
        }
    }
    
    function sortChilds($dataArr,$parentId,$child,$type)
    {
        if(!is_array($dataArr)||empty($dataArr)) return '';
        foreach ($dataArr as $k=>$v)
        {
            $allParents[$k] = $v['parent_id'];
        }
        if(!in_array($parentId,$allParents)) return ''; 
        foreach ($dataArr as $k=>$v)
        {
            if($v['parent_id']==$parentId)
            {
                $result[$k] = $v;
                $result[$k]['childs'] = $this->sortChilds($dataArr , $v['id']);
            }
        }
        return $result;
    }
    
    
}
