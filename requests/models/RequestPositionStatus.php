<?php

namespace detalika\requests\models;

use detalika\requests\models\base\RequestPositionStatus as BaseRequestPositionStatus;

class RequestPositionStatus extends BaseRequestPositionStatus
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