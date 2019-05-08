<?php

namespace detalika\picking\widgets;

use Yii;

trait UserIdTrait 
{
    protected function getUserId() 
    {
        if (Yii::$app->user === null) {
            throw new \Exception();   
        }
        
        return Yii::$app->user->id;
    }
}