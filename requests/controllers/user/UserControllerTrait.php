<?php

namespace detalika\requests\controllers\user;

use detalika\requests\actions\HideAction;
use detalika\requests\common\navigation\UserNavigationFactory;

trait UserControllerTrait 
{
    protected function getNavigationFactory()
    {
        return new UserNavigationFactory();
    }
    
    private function getHideAction()
    {
        return [
            'class' => HideAction::className(),
            'modelClass' => $this->getEditModelClassName(),
            // Так как у нас по-хитрому переопределен метод beforeValidate, то
            // валидацию мы отключаем.
            'isNeedValidateModel' => false,
        ];
    }
}