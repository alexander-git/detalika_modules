<?php

namespace detalika\auth\models\crud;

use detalika\auth\models\User as BaseUser;

class User extends BaseUser
{
    public function __toString() 
    {
        return $this->getTitleString();
    }
    
    public function getTitleString()
    {
        return '#' . $this->id . ' ' . $this->email;
    } 
}