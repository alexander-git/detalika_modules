<?php

namespace detalika\requests\models\forms;

use detalika\requests\helpers\StandartAttributesTrait;
use detalika\requests\models\RequestStatus;

class RequestStatusForm extends RequestStatus
{     
    use StandartAttributesTrait;
    
    public function init()
    {
        parent::init();
        
        // Чтобы  флажок для поля visible при создании записи был включён сразу.
        // При обновлении записи, поле будет переписано значением из БД.
        // После отправки формы в случае неудачной валидации в форме будет отображатся 
        // отправленное значение поля. Всё работает корректно.
        $this->visible = true;
    }
        
    public function getFormFields()  
    {
        return [
            [
                'displayOnly' => true,
                'attribute' => 'id',
            ],
            [
                'attribute' => 'name'
            ],
            $this->getSelectField('type', self::getTypesArray(), 'typeName'),
            $this->getVisibleField(),
            $this->getCreatedField(),
            $this->getUpdatedField(),
        ];
    }       
}