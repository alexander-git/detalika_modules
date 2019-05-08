<?php

namespace detalika\clients\models;

use execut\crudFields\Behavior;

use detalika\clients\models\base\CardType as BaseCardType;

class CardType extends BaseCardType
{  
    public function __toString() 
    {
        return $this->name;
    }
    
    public static function findByPk($id)
    {
        return CardType::findOne(['id' => $id]);
    }
    
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['fields'] = [
             'class' => Behavior::className(),
             'fields' => [
                 [
                     'attribute' => 'name',
                 ]
             ],
         ];
        
        return $behaviors;
    }
}