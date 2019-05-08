<?php

namespace detalika\requests\common;

use Yii;

use detalika\requests\helpers\OuterDependenciesTrait;
use detalika\requests\models\outer\ClientProfile;
use detalika\requests\models\base\ClientCar;

class CurrentUser
{
    use OuterDependenciesTrait;
    
    private static $_instance;
    
    /**
     * @return \detalika\requests\common\CurrentUser
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
        $this->userBelongInfo = UserBelongInfo::instance();
    }
    
    private $_userId;
    private $_clientProfileId;
    
    protected $userBelongInfo;
    
    public function isGuest()
    {
        return self::getOuterDependenciesStatic()->isCurrentUserGuest();
    }
    
    public function isClient()
    {
        return self::getOuterDependenciesStatic()->isCurrentUserClient();
    }
    
    public function getUserId()
    {
        if ($this->_userId !== null) {
            return $this->_userId;
        }
        
        $realUserId = $this->getRealUserId();
        
        // Если текущий пользователь может начинать подбор для клиента, то
        // проверим начат ли подбор и если это так, то возвращать будем 
        // id клиента, чтобы подбор осуществлялся как бы для него(клиента).
        $dependencies = self::getOuterDependenciesStatic();
        if (!$dependencies->canCurrentUserPicking()) {
            $this->_userId = $realUserId;
            return $this->_userId;
        } 
          
        $pickingClientProfileId = $dependencies->getPickingClientProfileIdByPickerId($realUserId);
        if ($pickingClientProfileId === null) {
            $this->_userId = $realUserId;
            return $this->_userId; 
        }

        $pickingUserId = $this->getUserIdByClientProfileId($pickingClientProfileId);
        if ($pickingUserId === null) {
            // Такого быть не должно.
            $this->_userId = $realUserId;
            return $this->_userId; 
        }

        $this->_userId = $pickingUserId;
        return $this->_userId;
    }
    
    public function getClientProfileId()
    {
        $realUserId = $this->getRealUserId();

        // Если текущий пользователь может начинать подбор для клиента, то
        // проверим начат ли подбор и если это так, то возвращать будем
        // id клиента, чтобы подбор осуществлялся как бы для него(клиента).
        $dependencies = self::getOuterDependenciesStatic();
        $pickingClientProfileId = $dependencies->getPickingClientProfileIdByPickerId($realUserId);
        if ($pickingClientProfileId !== null && $dependencies->canCurrentUserPicking()) {
            return $pickingClientProfileId;
        }

        if ($this->_clientProfileId === null) {   
            $userId = $this->getUserId();
            if ($userId === null) {
                return null;
            }
            
            $this->_clientProfileId = $this->getClinetProfileIdByUserId($userId);
        }
    
        return $this->_clientProfileId;
    }
            
    public function getClientCarIds()
    {
        return ClientCar::getClientCarIdsForClient($this->getClientProfileId());
    }
    
    /**
     * @param integer $clientCarId
     * @return boolean
     */
    public function hasClientCarOnId($clientCarId) 
    {
        $currentClientProfileId = $this->getClientProfileId();
        if ($currentClientProfileId === null) { 
            return false;
        }
        
        return $this->userBelongInfo->isClientCarIdBelongsToClient($clientCarId, $currentClientProfileId);
    }
    
    /**
     * @param integer $requestId
     * @return boolean
     */
    public function hasRequestOnId($requestId) 
    {
        $currentClientProfileId = $this->getClientProfileId();
        if ($currentClientProfileId === null) { 
            return false;
        }
        
        return $this->userBelongInfo->isRequestIdBelongsToClient($requestId, $currentClientProfileId);
    }
    
    /**
     * @param integer $requestMessageId
     * @return boolean
     */
    public function hasRequestMessageOnId($requestMessageId)
    {
        $userId = $this->getUserId();
        if ($userId === null) { 
            return false;
        }
        
        return $this->userBelongInfo->isRequestMessageIdBelongsToClient($requestMessageId, $userId);
    }
    
     /**
     * @param integer $requestPositionId
     * @return boolean
     */
    public function hasRequestPositionOnId($requestPositionId) 
    {
        $currentClientProfileId = $this->getClientProfileId();
        if ($currentClientProfileId === null) { 
            return false;
        }
        
        return $this->userBelongInfo->isRequestPositionIdBelongsToClient($requestPositionId, $currentClientProfileId);
    }
    
    public function hasClientCar($clientCar)
    {
        return $this->hasClientCarOnId($clientCar->id);
    }
    
    public function hasRequest($request) 
    {
        $currentClientProfileId  = $this->getClientProfileId();
        if ($currentClientProfileId === null) { 
            return false;
        }
        
       return (int) $request->clients_profile_id === (int) $currentClientProfileId;
    }
    
    public function hasRequestMessage($requestMessage)
    {
        $userId = $this->getUserId();
        if ($userId === null) { 
            return false;
        }
        
       return (int) $requestMessage->user_id === (int) $userId;
    }
    
    public function hasRequestPosition($requestPosition) 
    {
        return $this->hasRequestPositionOnId($requestPosition->id);
    }
    
    public function isPickingRequestPosition($requestPosition)
    {
        $userId = $this->getRealUserId();
        if ($userId === null) {
            return false;
        }
        
        foreach ($requestPosition->requestPositionUsers as $requestPositionUser) {
            if ($requestPositionUser->isBelongToUser($userId)) {
                return true;
            }
        }
        
        return false;
    }
    
    private function getClinetProfileIdByUserId($userId)
    {
        return self::getOuterDependenciesStatic()->getClinetProfileIdByUserId($userId);
    }
    
    private function getUserIdByClientProfileId($clientProfileId)
    {
        return self::getOuterDependenciesStatic()->getUserIdByClientProfileId($clientProfileId);
    }
    
    /**
     * В некоторых случаях функция userId может возвращать не реальный id 
     * пользователя, а, например, пользователя для которого совершается в 
     * данный момент подбор текущим пользоватлем комплектовщиком. 
     * Функция getRealUserId всегда возвращает системный id пользователя.
     * @return integer
     */
    private function getRealUserId()
    {
        return Yii::$app->user->id;
    }
}