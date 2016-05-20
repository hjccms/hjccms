<?php
/*
 * 管理菜单模型
 */
class SiteModel extends Model 
{
    //自动完成
    protected $_auto = array( array('create_time','time',1,'function') );
    //站点信息  支持全部和单个
    function getSite($condition)
    {
        
        $result = $this->where($condition)->order('id asc')->select();
        return $result;
    }

    //获取单个菜单信息
    function getInfo($id)
    {
        if(!$id) return '';
        $data['id'] = $id;
        $result = $this->where($data)->find();
        return $result;
    }
    //添加和更新菜单
    function addSite($data)
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
            $id = $this->add();
        }
        return $id; 
       
    }
    
    function checkSite($id)
    {
        if(!$id) return false;
        $data['id'] = $id;
        $result = $this->where($data)->find();
        $host = $_SERVER['HTTP_HOST'] ;
        if($host != $result['domain']){
            return false;
        }
        return true;
    }
}
