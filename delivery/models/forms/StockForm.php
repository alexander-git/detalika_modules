<?php

namespace detalika\delivery\models\forms;

use detalika\delivery\helpers\StandartAttributesTrait;
use detalika\delivery\models\Stock;

class StockForm extends Stock
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
        $isCreate = $this->isNewRecord;
        
        $fields = [
            'id' => [
                'attribute' => 'id',
                'displayOnly' => true,
                'visible' => !$isCreate
            ],
            'name' => [
                'attribute' => 'name',
            ],
            'address' => [
                'attribute' => 'address',
            ],
            'work_time' => [
                'attribute' => 'work_time',
            ],
            'ext_uuid' => [
                'attribute' => 'ext_uuid',
                'displayOnly' => true,
                'visible' => !$isCreate,
            ],
            'visible' => $this->getVisibleField(),
        ];

        if (!$isCreate) {
           $fields = array_merge($fields, [
                'created' => $this->getCreatedField(),
                'updated' => $this->getUpdatedField(),
            ]);
        }
        
        return $fields;
    }     
}