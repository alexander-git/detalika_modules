<?php

namespace detalika\requests\controllers;

use detalika\requests\common\navigation\AdminNavigationFactory;

trait AdminNavigationFactoryTrait 
{
    protected function getNavigationFactory()
    {
        return new AdminNavigationFactory();
    }
}