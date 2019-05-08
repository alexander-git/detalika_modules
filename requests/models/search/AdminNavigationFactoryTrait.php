<?php

namespace detalika\requests\models\search;

use detalika\requests\common\navigation\AdminNavigationFactory;

trait AdminNavigationFactoryTrait 
{
    public function getNavigationFactory()
    {
        return new AdminNavigationFactory();
    }
}