<?php

namespace detalika\requests\models;

use detalika\requests\models\base\RequestMessage as BaseRequestMessage;

class RequestMessage extends BaseRequestMessage
{       
    public function __toString() 
    {
        return $this->getTitleString();
    }
    
    public function getTitleString()
    {
         return 'Сообщение #' . $this->id;
    }
    
    
    // Переопределим отношения из базовой модели, так как в навигации 
    // при указании родительских элементов нам понадобиться их метод 
    // getTitleString().
    public function getRequest()
    {
        return $this->hasOne(Request::className(), ['id' => 'requests_request_id']);
    }
    
    public function getRequestPosition()
    {
        return $this->hasOne(RequestPosition::className(), ['id' => 'requests_request_position_id']);
    }
}