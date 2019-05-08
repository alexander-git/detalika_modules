<?php

namespace detalika\requests\validators\requestPosition;

use yii\validators\Validator;

class RequiredFieldsRequestPositionValidator extends Validator
{
    public $errorMessage = 'Нужно указать или название или артикул или товар или ссылку на поиск.';
    
    public function validateAttribute($model, $attribute)
    {
        if (
            empty($model->name) && 
            empty($model->goods_article_id) &&
            empty($model->goods_good_id) && 
            empty($model->link_to_search)
        ) {
            $this->addError($model, 'name', $this->errorMessage);
            $this->addError($model, 'goods_article_id', $this->errorMessage);
            $this->addError($model, 'goods_good_id', $this->errorMessage);
            $this->addError($model, 'link_to_search', $this->errorMessage);
        }
    }    
}
