<?php

namespace detalika\requests\models\outer;

use yii\db\ActiveRecord;
use detalika\requests\OuterTables;

class RequestPositionUser extends ActiveRecord
{
    public static function tableName() 
    {
        return OuterTables::getTableName('pickingRequestPositionUsers');
    }
     
    public static function getFieldName($fieldName)
    {
        return OuterTables::getFieldName('pickingRequestPositionUsers', $fieldName);
    } 
    
    public function isBelongToUser($userId)
    {
        $userIdFieldName = self::getFieldName('user_id');
        return (int) $this->$userIdFieldName === (int) $userId;
    }
}