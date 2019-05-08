<?php

namespace detalika\delivery\models;

use Yii;
use \detalika\delivery\models\base\Partner as BasePartner;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "delivery_partners".
 */
class Partner extends BasePartner
{

public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                # custom behaviors
            ]
        );
    }

    public function rules()
    {
        return ArrayHelper::merge(
             parent::rules(),
             [
                  # custom validation rules
             ]
        );
    }

    public function __toString()
    {
        return '#' . $this->id . ' ' . $this->name;
    }
}
