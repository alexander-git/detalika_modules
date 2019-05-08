<?php

namespace detalika\requests\common;

use yii\web\ForbiddenHttpException;
use detalika\requests\helpers\OuterDependenciesTrait;

class AccessCheck
{   
    use OuterDependenciesTrait;
    
    private static $_instance;
    
    protected $currentUser;
        
    /**
     * @return \detalika\requests\common\AccessCheck
     */
    public static function instance()
    {
        if (self::$_instance === null) {
            self::$_instance = new static();
        }
        
        return self::$_instance;
    }
    
    private function __construct() 
    {
        $this->currentUser = CurrentUser::instance();
    }

    public function canCurrentUserPicking()
    {
        return self::getOuterDependenciesStatic()->canCurrentUserPicking();
    }
    
    public function checkCanCurrentUserEditClientCar($clientCar)
    {
         if (!$this->currentUser->hasClientCar($clientCar) || !$clientCar->visible) {
             throw new ForbiddenHttpException();
         }
    }
    
    // В данный момент пользователь вообще не может редактировать и удалять запросы. 
    // Но этот метод используется для проверки может ли он просмотеть 
    // определённый запрос.
    public function checkCanCurrentUserEditRequest($request)
    {
         if (!$this->currentUser->hasRequest($request) || !$request->visible) {
             throw new ForbiddenHttpException();
         }
    }

    /*
    // В данный момент пользователь вообще не может редактировать и удалять позиции. 
    // Поэтому пока закомментируем это код.
    public function checkCanCurrentUserEditRequestPosition($requestPosition)
    {
         if (!$this->currentUser->hasRequestPosition($requestPosition) || !$requestPosition->visible) {
             throw new ForbiddenHttpException();
         }
    }
   */
    
    public function checkCanCurrentUserEditRequestMessage($requestMessage)
    {
        if (!$this->currentUser->hasRequestMessage($requestMessage) || !$requestMessage->visible) {
            throw new ForbiddenHttpException();
        }
    }
}