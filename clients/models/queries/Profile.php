<?php
/**
 * Created by PhpStorm.
 * User: execut
 * Date: 5/12/17
 * Time: 3:39 PM
 */

namespace detalika\clients\models\queries;


use yii\db\ActiveQuery;
use \detalika\clients\models;

class Profile extends ActiveQuery
{
    public function byContactValue($type, $value = null) {
        if (!is_array($type) && $value !== null) {
            $type = [
                'type' => $type,
                'value' => $value,
            ];
        }

        if (count($type) == 1) {
            $type = current($type);
            $where = $this->buildWhereByContactValue($type['type'], $type['value']);

            return $this->andWhere($where);
        }

        $where = ['OR'];
        foreach ($type as $item) {
            $where[] = $this->buildWhereByContactValue($item['type'], $item['value']);
        }

        return $this->andWhere($where);
    }

    /**
     * @param $type
     * @param $value
     * @return array
     */
    protected function buildWhereByContactValue($type, $value)
    {
        $where = [
            'id' => models\Contact::find()->byContactsTypeId(models\ContactType::find()->byType($type)->select('id'))->byValue($value)->select('clients_profile_id')
        ];
        return $where;
    }
}