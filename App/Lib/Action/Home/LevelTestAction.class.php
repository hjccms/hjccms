<?php
class LevelTestAction extends BaseAction {
    
    //测试登陆
    function testLogin(){
        $name = $this->_post('name');
        $mobile = $this->_post('mobile');
        if(!$name || !$mobile){
            $this->ajaxReturn('','操作失败',0);
        }
        session('levelTest',  json_encode(array('name'=>$name,'mobile'=>$mobile)));
        session('hashLevelTest',md5(encrypt(json_encode(array('name'=>$name,'mobile'=>$mobile)).cookie('PHPSESSID'),'E',C('APP_KEY'))));
        $this->ajaxReturn('','操作成功',1);
    }
    
    
    //ajax添加答案
    function addAnswer(){
        $num = $this->_post('num');
        $answer = $this->_post('answer');
        $obj = json_decode(session('levelTest')); 
        $info = D("Listening_test")->getInfoBySession($this->siteInfo->id);
        $all_answer = $info['schedule'];
        if($all_answer){
            $arr = json_decode(htmlspecialchars_decode($all_answer),true);
            foreach($arr as $k=>$v){
                if($num == $k){
                    $arr[$k] = $answer;
                }else{
                    $arr[$num] = $answer;
                }
            }
            $all_answer = json_encode($arr);
        }else{
            $all_answer = json_encode(array($num=>$answer));
        }
        if($all_answer){
            $data['site_id'] = $this->siteInfo->id;
            if($num == '25'){
                $right_answer = getListeningAnswer();
                $my_answer = json_decode(htmlspecialchars_decode($all_answer),true);
                $score = 0;
                foreach($my_answer as $k=>$v){
                    if($my_answer[$k] == $right_answer[$k]){
                        $score = $score+2;
                    }
                }
                $data['score'] = $score;
                $data['level'] = getListeningLevel($score);
                $data['answer'] = $all_answer;
            }
            $data['name'] = $obj->name;
            $data['mobile'] = $obj->mobile;
            $data['schedule'] = $all_answer;
            $data['create_time'] = time();
            if($info){
                $re = D("Listening_test")->where("`name`='{$obj->name}' and mobile='{$obj->mobile}' and site_id={$this->siteInfo->id}")->save($data);
            }else{
                $re = D("Listening_test")->add($data);
            }
            if($re){
                $this->ajaxReturn($all_answer,'操作成功',1);
            }else{
                $this->ajaxReturn('','操作失败',0);
            }
        }else{
            $this->ajaxReturn('','操作失败',0);
        }
        
    }
    
    //ajax获取答案
    function getAnswer(){
        $num = $this->_post('num');
        $answer = null;
        $info = D("Listening_test")->getInfoBySession($this->siteInfo->id);
        if($info['schedule']){
            $schedule = json_decode($info['schedule'],true);
            foreach($schedule as $k=>$v){
                if($num == $k){
                    $answer = $v;
                }
            }
        }
        if($answer){
            $this->ajaxReturn($answer,'操作成功',1);
        }else{
            $this->ajaxReturn('','操作失败',0);
        }
        
    }
    
}
