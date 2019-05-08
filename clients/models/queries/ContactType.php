<?php
/**
 * Created by PhpStorm.
 * User: execut
 * Date: 5/12/17
 * Time: 3:20 PM
 */

namespace detalika\clients\models\queries;


use yii\db\ActiveQuery;

class ContactType extends ActiveQuery
{
    public function byType($type) {
        return $this->andWhere([
            'type' => $type,
        ]);
    }

    public function isPhone() {
        return $this->byType(\detalika\clients\models\ContactType::TYPE_PHONE);
    }

    public function isEmail() {
        return $this->byType(\detalika\clients\models\ContactType::TYPE_EMAIL);
    }
}