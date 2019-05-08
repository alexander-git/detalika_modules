<?php
/**
 * Created by PhpStorm.
 * User: execut
 * Date: 5/11/17
 * Time: 4:06 PM
 */

namespace detalika\clients\models\queries;


use yii\db\ActiveQuery;

class Type extends ActiveQuery
{
    public function isIndividual() {
        return $this->andWhere([
            'type' => \detalika\clients\models\Type::TYPE_INDIVIDUAL,
        ]);
    }
}