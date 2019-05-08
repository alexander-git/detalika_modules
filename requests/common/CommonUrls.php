<?php

namespace detalika\requests\common;

use yii\helpers\Url;

class CommonUrls 
{
    private function __construct()
    {
        
    }
    
    // admin
    ////////////////////////////////////////////////////////////////////////////
    
    public static function getClientCarsUrlForAjaxList()
    {
        return Url::to(['client-car/index']);
    }
    
    public static function getRequestsUrlForAjaxList()
    {
        return Url::to(['request/index']);
    }
    
    public static function getRequestPositionsUrlForAjaxList()
    {
        return Url::to(['request-position/index']);
    }
    
    public static function getRequestStatuesUrlForAjaxList()
    {
        return Url::to(['request-status/index']);
    }
      
    public static function getRequestPositionStatuesUrlForAjaxList()
    {
        return Url::to(['request-position-status/index']);
    }
    
    public static function getRequestPositionParentSearchUrlForAjaxList()
    {
        return Url::to(['request-position/parents-search']);
    }
    
    // user
    ////////////////////////////////////////////////////////////////////////////
    
    public static function getUserClientCarsUrlForAjaxList()
    {
        return Url::to(['user/client-car/index']);
    }
    
    public static function getUserRequestsUrlForAjaxList()
    {
        return Url::to(['user/request/index']);
    }
    
    public static function getUserRequestPositionsUrlForAjaxList()
    {
        return Url::to(['user/request-position/index']);
    }
    
    public static function getUserRequestPositionParentSearchUrlForAjaxList()
    {
        return Url::to(['user/request-position/parents-search']);
    }
    
}