<?php

namespace detalika\requests\common\navigation;

class AdminNavigationFactory extends AbstractNavigationFactory
{
    /**
     * @return\detalika\requests\common\AdminRouteItems
     */
    public function createRouteItems()
    {
        return new AdminRouteItems();
    }
    
    /**
     * @return\detalika\requests\common\AdminNavigation
     */
    public function createNavigation()
    {
        return new AdminNavigation();
    }
    
    /**
     * @return\detalika\requests\common\AdminPageBuilder
     */
    public function createPageBuilder()
    {
        return new AdminPageBuilder();
    }
}