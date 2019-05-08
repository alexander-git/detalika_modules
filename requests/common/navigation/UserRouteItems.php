<?php

namespace detalika\requests\common\navigation;

class UserRouteItems extends AdminRouteItems 
{
    public function getClientCarControllerId()
    {
        return 'user/client-car';
    }
    
    public function getRequestControllerId()
    {
        return 'user/request';
    }
    
    public function getRequestPositionControllerId()
    {
        return 'user/request-position';
    }
    
    public function getRequestMessageControllerId()
    {
        return 'user/request-message';
    }
}