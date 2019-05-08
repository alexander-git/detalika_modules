<?php

namespace detalika\requests\validators;

use yii\validators\Validator;

class RequiredNotEmptyArrayValidator extends Validator 
{
    public $skipOnEmpty = false;
    
    public function validateAttribute($model, $attribute)
    {
        $errorMessage = "Необходимо заполнить «{attribute}».";
        $value = $model->$attribute;
        if (is_array($value) && count($value) === 0) {
            $this->addError($model, $attribute, $errorMessage);
        }
        
        if (!is_array($value) && empty($value)) {
            $this->addError($model, $attribute, $errorMessage);
        }
    }
}