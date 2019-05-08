<?php

namespace detalika\requests\models;

use detalika\requests\models\base\Request as BaseRequest;

class Request extends BaseRequest
{       
    public function __toString() 
    {
        return $this->getTitleString();
    }
    
    public function getTitleString()
    {
        return 'Запрос #' . $this->id;
    } 
    
    // Переопределим отношение из базовой модели, так как в навигации 
    // контроллера запроса в каких-то случаях нам нужно сделать 
    // ссылку на автомобиль к которому относится запрос.
    // А значит нужен его(автомобиля) метод getTitleString(). 
    public function getClientCar() 
    {
        return $this->hasOne(ClientCar::className(), ['id' => 'requests_client_car_id']);
    }
}