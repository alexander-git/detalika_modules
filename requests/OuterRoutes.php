<?php

namespace detalika\requests;

use detalika\requests\helpers\OuterDependenciesTrait;

class OuterRoutes
{
    use OuterDependenciesTrait;
    
    private static $_routes = null;

    
    private static function getRoutes() 
    {
        if (self::$_routes === null) {
            $dependencies = self::getOuterDependenciesStatic();
            
            self::$_routes = [
                'carsMarks' => $dependencies->getCarsMarksAjaxRoute(),
                'carsModels' => $dependencies->getCarsModelsAjaxRoute(),
                'carsModifications' => $dependencies->getCarsModificationsAjaxRoute(),
                'carsModificationsEngines' => $dependencies->getCarsModificationsEnginesAjaxRoute(),
                'clientsProfiles' => $dependencies->getClientsProfilesAjaxRoute(),
                'users' => $dependencies->getUsersAjaxRoute(),
                'goods' => $dependencies->getGoodsAjaxRoute(),
                'detailsArticles' => $dependencies->getDetailsArticlesAjaxRoute(),
                'deliveryPartners' => $dependencies->getDeliveryPartnersAjaxRoute(),
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