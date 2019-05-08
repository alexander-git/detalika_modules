<?php

namespace detalika\requests\validators;

use yii\validators\Validator;

use detalika\requests\common\CurrentUser;

class OnlyCurrentUserRequestsValidator extends Validator 
{
    public function validateAttribute($model, $attribute)
    {
        $requestId = $model->$attribute;
        if (!CurrentUser::instance()->hasRequestOnId($requestId)) {
            $this->addError($model, $attribute, 'Запрос должен относиться к текущему пользователю.');
        }
    }
}