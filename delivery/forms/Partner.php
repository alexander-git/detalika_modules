<?php
/**
 * Created by PhpStorm.
 * User: execut
 * Date: 2/14/17
 * Time: 11:11 AM
 */

namespace detalika\delivery\forms;

use execut\actions\action\adapter\gridView\ModelHelper;
use yii\helpers\ArrayHelper;

class Partner extends \detalika\delivery\models\Partner
{
    use ModelHelper;
    public function getFormFields() {
        $fields = $this->getStandardFields();
        return ArrayHelper::merge($fields, [
        ]);
    }
}