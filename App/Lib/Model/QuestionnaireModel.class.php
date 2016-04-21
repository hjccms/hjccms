<?php
class QuestionnaireModel extends Model {
    
    function getQuestionnaire($condition,$region='false'){
        $result = $this->where($condition)->order('id asc')->select();
        if(!$region){
            return $result;
        }
        return $result;
    }
    
    
    
}
