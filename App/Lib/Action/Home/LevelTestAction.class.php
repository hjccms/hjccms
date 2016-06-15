<?php
class LevelTestAction extends BaseAction {
    
    //测试登陆
    function testLogin(){
        $name = $this->_post('name');
        $mobile = $this->_post('mobile');
        if(!$name || !$mobile){
            $this->ajaxReturn('','操作失败',0);
        }
        //纪录英文名
        D("Listening_test")->where("mobile='{$mobile}' and site_id={$this->siteInfo->id}")->save(array("name"=>$name));
        
        //设置session
        session('levelTest',  json_encode(array('name'=>$name,'mobile'=>$mobile)));
        session('hashLevelTest',md5(encrypt(json_encode(array('name'=>$name,'mobile'=>$mobile)).cookie('PHPSESSID'),'E',C('APP_KEY'))));
        
        //数据同步
        D("Listening_test")->aoniListeningTestData($mobile,$this->siteInfo->id);
        
        $this->ajaxReturn('','操作成功',1);
    }
    
    
    //ajax添加答案
    function addAnswer(){
        $num = $this->_post('num');
        $answer = $this->_post('answer');
        $obj = json_decode(session('levelTest')); 
        $info = D("Listening_test")->getInfoBySession($this->siteInfo->id);
        if(!$info && !$obj){
            $this->ajaxReturn('','操作失败',0);
        }
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
            if($num == '25'){
                $right_answer = get_listening_answer();
                $my_answer = json_decode(htmlspecialchars_decode($all_answer),true);
                $score = 0;
                foreach($my_answer as $k=>$v){
                    if($my_answer[$k] == $right_answer[$k]['answer']){
                        if($right_answer[$k]['score']){
                            $score = $score+$right_answer[$k]['score'];
                        }
                    }
                }
                $data['score'] = $score;
                $data['level'] = get_listening_level($score);
                $data['answer'] = $all_answer;
                $data['schedule'] = '';
            }else{
                $data['schedule'] = $all_answer;
            }
            if($info){
                $re = D("Listening_test")->where("mobile='{$obj->mobile}' and site_id={$this->siteInfo->id}")->save($data);
            }else{
                $data['site_id'] = $this->siteInfo->id;
                $data['student_id'] = $this->userInfo->id;
                $data['name'] = $obj->name;
                $data['mobile'] = $obj->mobile;
                $data['create_time'] = time();
                $re = D("Listening_test")->add($data);
            }
            
            //数据同步
            if($num == '25'){
                D("Listening_test")->aoniListeningTestData($obj->mobile,$this->siteInfo->id,1);
            }
            
            $this->ajaxReturn('','操作成功',1);
        }else{
            $this->ajaxReturn('','操作失败',0);
        }
        
    }
    
    //ajax获取答案
    function getAnswer(){
        $num = $this->_post('num');
        $answer = null;
        $info = D("Listening_test")->getInfoBySession($this->siteInfo->id);
        if($info['schedule'] || $info['answer']){
            $schedule = json_decode($info['schedule'],true);
            $answers = json_decode($info['answer'],true); 
            foreach($schedule as $k=>$v){
                if($num == $k){
                    $answer = $v;
                }
            }
            if(!$answer){
                foreach($answers as $k=>$v){
                    if($num == $k){
                        $answer = $v;
                    }
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
