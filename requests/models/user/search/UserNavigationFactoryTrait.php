<?php

namespace detalika\requests\models\user\search;

use detalika\requests\common\navigation\UserNavigationFactory;

trait UserNavigationFactoryTrait 
{
    public function getNavigationFactory()
    {
        return new UserNavigationFactory();
    }
}