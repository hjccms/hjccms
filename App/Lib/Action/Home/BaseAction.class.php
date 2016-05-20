<?php
/*
 * 后台控制器基础类
 */
class BaseAction  extends Action 
{
    var $siteInfo;
    function __construct() 
    {
        parent::__construct();
        $this->siteInfo = cookie('siteInfo');
        $url = get_hosturl();
        $url = atrim($url,array('http://','https://'),'1','1');
        if(empty($this->siteInfo->id)||$this->siteInfo->domain!=$url)
        {
            $siteInfo = $this->getSiteInfo($url);
            if(empty($siteInfo))    $this->error('您访问的站点不存在或已经被关闭，请联系管理员！');
            if(!is_dir(APP_PATH.'Tpl/'.$siteInfo['template'])||!$siteInfo['template'])     $this->error('您的站点正在开发中...');
            cookie('siteInfo',$siteInfo,3600*4);
            $this->siteInfo = (object)$siteInfo; 
        }
        $this->assign('siteInfo',  get_object_vars($this->siteInfo));
    }
    function display($templateFile='',$charset='',$contentType='',$content='',$prefix='')
    {
        $Xp = $this->siteInfo->template;
        if(!is_dir(APP_PATH.'Tpl/'.$Xp)||!$Xp) //如果模板不存在 只供error调用 默认使用父级方法
        {
            parent::display($templateFile,$charset,$contentType,$content,$prefix);
            die();
        }
        parent::display($Xp.':'.MODULE_NAME.':'.ACTION_NAME);
    }
    //获取站点信息
    function getSiteInfo($url)
    {
        
        $info = D('Site')->getSite(array('domain'=>$url));
        
        if(count($info)>1)  $this->error('内部错误，请联系管理员！');
        $info = $info[0];
        if(!$info) return false;
        if($info['valid']!=1) return false;
        
        return $info;
    }
}

