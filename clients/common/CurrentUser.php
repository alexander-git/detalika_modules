<?php

namespace detalika\clients\common;

use Yii;

use detalika\clients\models\Profile;

class CurrentUser
{
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
   
    }
    
    private $_clientProfileId;
    
    public function getUserId()
    {
        return Yii::$app->user->id;
    }
    
    public function getClientProfileId()
    {        

        if ($this->_clientProfileId == null) {   
            $userId = $this->getUserId();
            if ($userId === null) {
                return null;
            }

            $profile = Profile::findOne(['user_id' => $userId]);     
            if ($profile === null) {
                return null;    
            } else {
                $this->_clientProfileId = $profile->id;
                
            }
        }
    
        return $this->_clientProfileId;
    }
    
    public function isPickingProfile($profile)
    {
        $userId = $this->getUserId();
        if ($userId === null) {
            return false;
        }
        
        foreach ($profile->profileUsers as $profileUser) {
            if ($profileUser->isBelongToUser($userId)) {
                return true;
            }
        }
        
        return false;
    }
    
}