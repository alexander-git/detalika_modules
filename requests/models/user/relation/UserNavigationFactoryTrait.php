<?php

namespace detalika\requests\models\user\relation;

use detalika\requests\common\navigation\UserNavigationFactory;

trait UserNavigationFactoryTrait 
{
    public function getNavigationFactory()
    {
        return new UserNavigationFactory();
    }
}