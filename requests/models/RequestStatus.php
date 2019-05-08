<?php

namespace detalika\requests\models;

use detalika\requests\models\base\RequestStatus as BaseRequestStatus;

class RequestStatus extends BaseRequestStatus
{   
    public function __toString() 
    {
        return $this->getTitleString();
    }
    
    public function getTitleString()
    {
        return '#' . $this->id . ' ' . $this->name;
    } 
}