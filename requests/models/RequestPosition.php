<?php

namespace detalika\requests\models;

use detalika\requests\models\base\RequestPosition as BaseRequestPosition;

class RequestPosition extends BaseRequestPosition
{   
    public function __toString() 
    {
        return $this->getTitleString();
    }
    
    public function getTitleString()
    {
        return 'Позиция # '. $this->id;
    }
    
    // Переопределим отношение из базовой модели, так как в навигации 
    // контроллера позиции запроса нам нужно сделать ссылку на родительский.
    // А значит нужен его(запроса) метод getTitleString(). 
    public function getRequest()
    {
        return $this->hasOne(Request::className(), ['id' => 'requests_request_id']);
    }
}