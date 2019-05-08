<?php

namespace detalika\clients\models;

use detalika\clients\models\base\User as BaseUser;

class User extends BaseUser
{   
    public function getUserEmail() {
        return $this->profile->email;
    }
}    
