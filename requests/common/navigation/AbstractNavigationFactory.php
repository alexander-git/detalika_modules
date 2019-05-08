<?php

namespace detalika\requests\common\navigation;

abstract class AbstractNavigationFactory
{
    abstract public function createRouteItems();
    abstract public function createNavigation();
    abstract public function createPageBuilder();
}