<?php

namespace detalika\picking;

use Yii;

class OuterDependenciesDefault implements OuterDependenciesInterface
{
    public function getModuleId() { return 'picking'; }
    
    public function canCurrentUserPicking() 
    {
        if (Yii::$app->user->isGuest) {
            return false;
        }
        
        return true;
        
        return Yii::$app->user->can('picking');        
    }
}
