<?php

namespace detalika\requests\common\navigation;

class AdminRouteItems
{
    public function getClientCarControllerId()
    {
        return 'client-car';
    }
    
    public function getRequestControllerId()
    {
        return 'request';
    }
    
    public function getRequestPositionControllerId()
    {
        return 'request-position';
    }
    
    public function getRequestMessageControllerId()
    {
        return 'request-message';
    }
    
    public function getRequestStatusControllerId()
    {
        return 'request-status';
    }
    
    public function getRequestPositionStatusControllerId()
    {
        return 'request-position-status';
    }
}
