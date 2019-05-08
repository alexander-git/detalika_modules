<?php

namespace detalika\picking\common;

use Yii;
use detalika\picking\helpers\OuterDependenciesTrait;

class AccessControlTools 
{    
    use OuterDependenciesTrait;
    
    public static function pickingMatchCallback(
        $rule, 
        $action
    ) {
        if (Yii::$app->user->isGuest) {
            return false;
        }
                
        return self::getOuterDependenciesStatic()->canCurrentUserPicking();
    }
       
}