<?php

namespace detalika\requests\common\navigation;

use Yii;

class AdminNavigation
{
    const FROM_CLIENT_CAR_GET_PARAM_NAME = 'fromClientCar';
    const FROM_REQUEST_GET_PARAM_NAME = 'fromRequest';
    const FROM_REQUEST_POSITION_GET_PARAM_NAME = 'fromRequestPosition';
    // Значение from Get-параметров не важно. 
    // Для работы будет важно наличие или отсутствие get-параметра.
    const FROM_GET_PARAM_VALUE = '1';
    
    protected $pagesToPossibleFromGetParams = null;
    protected $routeItems;
    
    
    const NULL_CONTROLLER_KEY = '0';
    
    public function __construct()
    {
        $this->routeItems = new AdminRouteItems();
    }
    
    private function getAllFromGetParamNames()
    {
        return [
            self::FROM_CLIENT_CAR_GET_PARAM_NAME,
            self::FROM_REQUEST_GET_PARAM_NAME,
            self::FROM_REQUEST_POSITION_GET_PARAM_NAME
        ];
    }
    
    final protected function getPagesToPossibleFromGetParams()
    {
        if ($this->pagesToPossibleFromGetParams === null) {
            $this->initPagesToPossibleFromGetParams();
        }
        
        return $this->pagesToPossibleFromGetParams;
    }
    
    protected function initPagesToPossibleFromGetParams()
    {
        $cc = self::FROM_CLIENT_CAR_GET_PARAM_NAME;
        $r = self::FROM_REQUEST_GET_PARAM_NAME;
        $rp = self::FROM_REQUEST_POSITION_GET_PARAM_NAME;

        $clientCarControllerId = $this->routeItems->getClientCarControllerId();
        $requestControllerId = $this->routeItems->getRequestControllerId();
        $requestPositionControllerId = $this->routeItems->getRequestPositionControllerId();
        $requestMessageControllerId = $this->routeItems->getRequestMessageControllerId();
        $requestStatusControllerId = $this->routeItems->getRequestStatusControllerId();
        $requestPositionStatusControllerId = $this->routeItems->getRequestPositionStatusControllerId();

        $this->pagesToPossibleFromGetParams = [
            self::NULL_CONTROLLER_KEY => [
                'main' => [],
            ],

            $clientCarControllerId => [
                'index' => [],
                'create' => [],
                'update' => [],
            ],

            $requestControllerId => [
                'index' => [],
                'create' => [$cc],
                'update' => [$cc],
            ],

            $requestPositionControllerId => [
                'index' => [],
                'create' => [$cc, $r],
                'update' => [$cc, $r],
                'mass-create' => [$cc, $r],
            ],

            $requestMessageControllerId => [
                'index' => [],
                'create' => [$cc, $r, $rp],
                'update' => [$cc, $r, $rp],
            ],

            $requestStatusControllerId => [
                'index' => [],
                'create' => [],
                'update' => [],
            ],

            $requestPositionStatusControllerId => [
                'index' => [],
                'create' => [],
                'update' => [], 
            ],
        ];
    }
    
    /**
     * @param array $baseRoute
     * @param string pageKey
     * @return array
     */
    public function getRouteForPageWithExisting($baseRoute, $controllerId, $pageKey) 
    {
        if ($controllerId === null) {
            $controllerKey = self::NULL_CONTROLLER_KEY;
        } else {
            $controllerKey = $controllerId;
        }
        
        $pagesToPossibleFromGetParams = $this->getPagesToPossibleFromGetParams();
        
        if (!$pagesToPossibleFromGetParams[$controllerKey]) {
            throw new \Exception($controllerKey);
        }
        
        if (!isset($pagesToPossibleFromGetParams[$controllerKey][$pageKey])) {
            throw new \Exception($controllerKey.' '.$pageKey);
        }
        
        $possibleParamsList = $pagesToPossibleFromGetParams[$controllerKey][$pageKey];
        return $this->getRouteWithExistingFromGetParams($baseRoute, $possibleParamsList);
    }
      
    public function getRouteWithExisting($baseRoute)
    {
        return $this->getRouteWithAllPosiibleExistingFromGetParams($baseRoute);
    }
    
    public function isMoveFromClientCarPage()
    {
        return $this->isMoveFromPage(self::FROM_CLIENT_CAR_GET_PARAM_NAME);
    }
    
    public function isMoveFromRequestPage()
    {
        return $this->isMoveFromPage(self::FROM_REQUEST_GET_PARAM_NAME);
    }
    
    public function isMoveFromRequestPositionPage()
    {
        return $this->isMoveFromPage(self::FROM_REQUEST_POSITION_GET_PARAM_NAME);
    }
        
    public function getRouteFromClientCarExisitng($baseRoute)
    {
        return $this->getRouteFromWithExisting($baseRoute, self::FROM_CLIENT_CAR_GET_PARAM_NAME);
    }
    
    public function getRouteFromRequestExisitng($baseRoute)
    {
        return $this->getRouteFromWithExisting($baseRoute, self::FROM_REQUEST_GET_PARAM_NAME);
    }
    
    public function getRouteFromRequestPositionExisitng($baseRoute)
    {
        return $this->getRouteFromWithExisting($baseRoute, self::FROM_REQUEST_POSITION_GET_PARAM_NAME);
    }
    
    
    private function getRouteWithExistingFromGetParams($baseRoute, $possibleParamsList) 
    {
        $route = $baseRoute;
        foreach ($possibleParamsList as $paramName) {
            $paramValue = Yii::$app->request->get($paramName, null);   
            if ($paramValue !== null) {
                if (!isset($route[$paramName])) {
                    $route[$paramName] = $paramValue;
                } 
            }
        }
        return $route;
    }
    
    /**
     * @param array $baseRoute
     * @return array
     */
    private function getRouteWithAllPosiibleExistingFromGetParams($baseRoute) 
    {
        $allPossibleParams = $this->getAllFromGetParamNames();
        return $this->getRouteWithExistingFromGetParams($baseRoute, $allPossibleParams);
    }
     
    private function isMoveFromPage($fromGetParamName)
    {
        $fromGetParamValue = Yii::$app->request->get($fromGetParamName, null);    
        return $fromGetParamValue !== null;
    }  
        
    private function getRouteFrom($baseRoute, $paramName)
    {
        $route = $baseRoute;
        if (!isset($route[$paramName])) {
            $route[$paramName] = self::FROM_GET_PARAM_VALUE;
        }
        
        return $route;
    }
    
    private function getRouteFromWithExisting($baseRoute, $paramName)
    {
        $routeWithExistingFromGetParams = $this->getRouteWithAllPosiibleExistingFromGetParams($baseRoute);
        return $this->getRouteFrom($routeWithExistingFromGetParams, $paramName);
    }
}