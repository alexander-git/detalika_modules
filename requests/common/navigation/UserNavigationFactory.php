<?php

namespace detalika\requests\common\navigation;

class UserNavigationFactory extends AbstractNavigationFactory
{
    /**
     * @return\detalika\requests\common\UserRouteItems
     */
    public function createRouteItems()
    {
        return new UserRouteItems();
    }
    
    /**
     * @return\detalika\requests\common\UserNavigation
     */
    public function createNavigation()
    {
        return new UserNavigation();
    }
    
    /**
     * @return\detalika\requests\common\UserPageBuilder
     */
    public function createPageBuilder()
    {
        return new UserPageBuilder();
    }
}
