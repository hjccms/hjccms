<?php
class QuestionnaireModel extends Model {
    
    function getQuestionnaire($condition){
        $result = $this->where($condition)->order('id asc')->select();
        return $result;
    }
    
    
    
}
