<?php
/*
 * 管理菜单模型
 */
class Field_infoModel extends Model 
{
    
    //站点信息  支持全部和单个
    function getModel($modelId='')
    {
        if($modelId&&  intval($modelId)>0) $condition = array('id'=>$modelId);
        $ret = $this->where($condition)->order("id asc")->select();
        if(!empty($modelId))            return $ret['0'];  //如果查询单个信息直接返回 数组
        else return $ret;
    }
    
    //添加和更新菜单
    function addField($data)
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
            if($this->createField($data)) $id = $this->add();
            else return false;
        }
        return $id; 
       
    }
    
    //创建数据库
    function createField($data)
    {
        $modelName = D('Model')->where("id=".$data['model_id'])->getField('table_name');
        $fieldType = C('FIELDTYPE');
       
        $type = $fieldType[$data['type']]['fieldType'];
      
        $type .= ($fieldType[$data['type']]['longth']&&intval($data['field_longth'])>0)?'('.$data['field_longth'].')':'';
        $sql = '﻿ALTER TABLE `'.C('DB_PREFIX').$modelName.'` ADD COLUMN `'.$data['field'].'`  '.$type.' NOT NULL ;';
        echo $sql;
        if(mysql_query($sql)) return true;
        else die(mysql_error());
        
    }
    function checkField($filed,$param)
    {
        $condition = array($filed=>$param);
        $ret = $this->where($condition)->getField('id');
        if($ret) return true; else return false;
    }
}
