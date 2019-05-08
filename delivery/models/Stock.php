<?php

namespace detalika\delivery\models;

use detalika\delivery\models\base\Stock as BaseStock;

class Stock extends BaseStock
{
    public function __toString() 
    {
        return $this->getTitleString();
    }
    
    public function getTitleString()
    {
        return 'Склад ' . $this->id . ' "'. $this->name .'"'; 
    }   
}