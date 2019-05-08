<?php

namespace detalika\requests\models\relation;

use detalika\requests\common\navigation\AdminNavigationFactory;

trait AdminNavigationFactoryTrait 
{
    public function getNavigationFactory()
    {
        return new AdminNavigationFactory();
    }
}