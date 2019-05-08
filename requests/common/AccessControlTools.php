<?php

namespace detalika\requests\common;

use Yii;

use detalika\requests\helpers\OuterDependenciesTrait;

class AccessControlTools 
{
    use OuterDependenciesTrait;
    
    public static function adminMatchCallback(
        $rule, 
        $action, 
        $actionsAvailableForAuthorizedUsersViaAjax = []
    ) {
        if (Yii::$app->user->isGuest) {
            return false;
        }
                
        if (Yii::$app->request->isAjax) {
            if (in_array($action->id, $actionsAvailableForAuthorizedUsersViaAjax)) {
                return true;
            }
        }
     
        return self::getOuterDependenciesStatic()->canCurrentUserAdmin();
    }
        
    
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