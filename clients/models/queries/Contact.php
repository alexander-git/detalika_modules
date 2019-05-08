<?php
/**
 * Created by PhpStorm.
 * User: execut
 * Date: 5/12/17
 * Time: 3:15 PM
 */

namespace detalika\clients\models\queries;

use yii\db\ActiveQuery;

class Contact extends ActiveQuery
{
    public function byContactsTypeId($id) {
        return $this->andWhere([
            'clients_contacts_type_id' => $id,
        ]);
    }

    public function byValue($value) {
        return $this->andWhere([
            'value' => $value,
        ]);
    }
}