<?php

namespace detalika\requests\validators;

use yii\validators\Validator;

use yii\base\InvalidConfigException;

class UniqueItemFieldInArrayValidator extends Validator 
{
    public $addErrorToRowItem = false;
    public $fieldName;
    
    public function init()
    {
        parent::init();
        if (empty($this->fieldName)) {
            throw new InvalidConfigException('Значение свойтва fieldName должно быть установлено.');
        }
        if (empty($this->message)) {
            $this->message = "Значение не должно повторяться.";   
        }
    }
    
    public function validateAttribute($model, $attribute)
    {
        $items = $model->$attribute;  
        if (count($items) === 0) {
            return;
        }
        
        $equalContactTypeIdIndexes = [];
        foreach($items as $i => $iRow){
            foreach ($items as $j => $jRow) {
                if ($i === $j) {
                    continue;
                }

                if ($iRow[$this->fieldName] === $jRow[$this->fieldName]) {
                    $equalContactTypeIdIndexes []= $i;
                    $equalContactTypeIdIndexes []= $j;
                }
            }
        }
        
        $uniqueIndexes = array_unique($equalContactTypeIdIndexes);
        if (count($uniqueIndexes) > 0) {
            if ($this->addErrorToRowItem) {
                 foreach ($uniqueIndexes as $index) {
                    $key = $attribute . '[' . $index . '][' . $this->fieldName. ']';    
                    $model->addError($key, $this->message);
                }    
            } else {
                $model->addError($attribute, $this->message);   
            }    
        }
    }
}