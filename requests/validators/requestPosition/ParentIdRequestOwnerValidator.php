<?php

namespace detalika\requests\validators\requestPosition;

use yii\validators\Validator;

use detalika\requests\models\base\RequestPosition;

class RequiredFieldsRequestPositionValidator extends Validator
{
    public $errorMessage = 'Родитель должен относиться к тому же запросу, что и текущая позиция';
    
    public function validateAttribute($model, $attribute)
    {
        if (empty($model->request_id)) {
            return;
        }
        
        if (empty($model->parent_id)) {
            return;
        }
 
        $parent = RequestPosition::findOne(['id' => $model->parent_id]);
        if ((int) $parent->request_id !== (int) $parent->request_id) {
            $this->addError($model, $attribute, $this->errorMessage);
        }
    }    
}
