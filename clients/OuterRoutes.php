<?php

namespace detalika\clients;

use detalika\clients\helpers\OuterDependenciesTrait;

class OuterRoutes
{
    use OuterDependenciesTrait;
    
    private static $_routes = null;

    private static function getRoutes() 
    {
        if (self::$_routes === null) {
            $dependencies = self::getOuterDependenciesStatic();
            
            self::$_routes = [
                'authUsers' => $dependencies->getAuthUsersAjaxRoute(),
            ];
        }
        
        return self::$_routes;
    }
    
    public static function getRoute($routeName) 
    {   
        if (isset(self::getRoutes()[$routeName])) {
             return self::getRoutes()[$routeName];
        } else {
            throw new \Exception();
        }
    }
    
    private function __construct() 
    {

    }
}