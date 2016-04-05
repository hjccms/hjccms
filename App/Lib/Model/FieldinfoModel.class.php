<?php
/*
 * 管理菜单模型
 */
class FieldinfoModel extends Model 
{
    
    //站点信息  支持全部和单个
    function getField($modelId='',$con=array())
    {
        if($modelId&&  intval($modelId)>0) $condition = array('model_id'=>$modelId);
        if(!empty($con)&&  is_array($con)) $condition = array_merge ($condition,$con);
        $ret = $this->where($condition)->order("sort asc")->select();
        return $ret;
    }
    function getInfo($id='')
    {
        if($id&&  intval($id)>0) $condition = array('id'=>$id);
        $ret = $this->where($condition)->find();
        return $ret;
    }
    
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
            
            if($this->changeField($data))
            {
                if($this->save($data)) $id = $data['id'];
                else return false;
            }
            else
            {
                return false; 
            }
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
        $sql = 'ALTER TABLE `'.C('DB_PREFIX').$modelName.'` ADD COLUMN `'.$data['field_name'].'`  '.$type.' NOT NULL ';
       
        if(mysql_query($sql)) return true;
        else return false;
        
    }
    //创建数据库
    function changeField($data)
    {
        $modelName = D('Model')->where("id=".$data['model_id'])->getField('table_name');
        $oldField = $this->where("id=".$data['id'])->field('field_name')->find();
        
        if($data['field_name']==$oldField) return true;
        $fieldType = C('FIELDTYPE');
       
        $type = $fieldType[$data['type']]['fieldType'];
      
        $type .= ($fieldType[$data['type']]['longth']&&intval($data['field_longth'])>0)?'('.$data['field_longth'].')':'';
        $sql = 'ALTER TABLE `'.C('DB_PREFIX').$modelName.'` CHANGE COLUMN `'.$oldField['field_name'].'` `'.$data['field_name'].'`  '.$type.' NOT NULL ';
       
        if(mysql_query($sql)) return true;
        else return false;
        
    }
    function checkField($filed,$param,$con=array())
    {
        $condition = array($filed=>$param);
        $condition = array_merge($condition,$con);
        $ret = $this->where($condition)->field('name')->find();
        if($ret) return true; else return false;
    }
    //删除字段
    function delField($modelId,$id)
    {
        $modelName = D('Model')->where("id=".$modelId)->getField('table_name');
        $arr = $this->where("id='$id'")->field("field_name")->find();
        $field = $arr['field_name'];
        
        if(!$modelName||!$field) return false;
        $sql = 'alter table `'.C('DB_PREFIX').$modelName.'` drop column   '.$field;
        if(mysql_query($sql))
        {
            //删除记录
            $this->where("id=".$id)->delete();
            return true;
        }
        else
        {
            die(mysql_error());
            return false;
        }
    }
    
}
