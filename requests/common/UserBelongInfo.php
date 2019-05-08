<?php

namespace detalika\requests\common;

use detalika\requests\models\base\ClientCar;
use detalika\requests\models\base\Request;
use detalika\requests\models\base\RequestPosition;
use detalika\requests\models\base\RequestMessage;

class UserBelongInfo
{
    private static $_instance;
    
    
    // Ключи id профиля или пользователя. Значения - списки Id-шников записей связанных 
    // с этим клиентом.
    private $_profileClientCarIds = [];
    private $_profileRequestIds = [];
    private $_userRequestMessageIds = [];
    private $_profileRequestPositionIds = [];
    
    /**
     * @return \detalika\requests\common\UserBelongInfo
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
        
    }
    
    public function isClientCarIdBelongsToClient($clientCarId, $clientProfileId)
    {   
        $key = (int) $clientProfileId;
        if (!isset($this->_profileClientCarIds[$key])) {
            $this->_profileClientCarIds[$key] = ClientCar::getClientCarIdsForClient($clientProfileId);
        }

        return in_array((int) $clientCarId, $this->_profileClientCarIds[$key]);
    }
    
    public function isRequestIdBelongsToClient($requestId, $clientProfileId)
    {   
  
        $key = (int) $clientProfileId;
        if (!isset($this->_profileRequestIds[$key])) {
            $this->_profileRequestIds[$key] = Request::getRequestIdsForClient($clientProfileId);
        }

        return in_array((int) $requestId, $this->_profileRequestIds[$key]);
    }
    
    public function isRequestMessageIdBelongsToClient($requestMessageId, $userId)
    {
        $key = (int) $userId;
        if (!isset($this->_userRequestMessageIds[$key])) {
            $this->_userRequestMessageIds[$key] = RequestMessage::getRequestMessageIdsForUser($userId);
        }

        return in_array((int) $requestMessageId, $this->_userRequestMessageIds[$key]);
    } 
    
    public function isRequestPositionIdBelongsToClient($requestPositionId, $clientProfileId)
    {
        $key = (int) $clientProfileId;
        if (!isset($this->_profileRequestPositionIds[$key])) {
            $requestIds = Request::getRequestIdsForClient($clientProfileId);
            if (count($requestIds) === 0) {
                $this->_profileRequestPositionIds[$key] = [];
            } else {
                $this->_profileRequestPositionIds[$key] = RequestPosition::getRequestPositionIdsForRequests($requestIds);
            }
        }

        return in_array((int) $requestPositionId, $this->_profileRequestPositionIds[$key]);
    } 
}