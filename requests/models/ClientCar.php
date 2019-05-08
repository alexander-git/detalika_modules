<?php

namespace detalika\requests\models;

use detalika\requests\models\base\ClientCar as BaseClientCar;

class ClientCar extends BaseClientCar
{   
    public function __toString() 
    {
        return $this->getTitleString();
    }
    
    public function getTitleString()
    {
        return $this->carShortName;
    }    
}