<?php

namespace detalika\requests\common\navigation;

class UserNavigation extends AdminNavigation
{
    public function __construct()
    {
        parent::__construct();
        $this->routeItems = new UserRouteItems();
    }
    
    protected function initPagesToPossibleFromGetParams()
    {
        parent::initPagesToPossibleFromGetParams();
        
        $cc = self::FROM_CLIENT_CAR_GET_PARAM_NAME;
        $requestControllerId = $this->routeItems->getRequestControllerId();
        
        if (!isset($this->pagesToPossibleFromGetParams[$requestControllerId])) {
            $this->pagesToPossibleFromGetParams[$requestControllerId] = [];
        }
        
        $this->pagesToPossibleFromGetParams[$requestControllerId]['view'] = [$cc];
    }
}