<?php

namespace detalika\clients\models\outer;
use yii\db\ActiveRecord;

use detalika\clients\OuterTables;

class ProfileUser extends ActiveRecord
{  
    public static function tableName() 
    {
        return OuterTables::getTableName('pickingProfileUsers');
    }
    
    public static function getFieldName($fieldName)
    {
        return OuterTables::getFieldName('pickingProfileUsers', $fieldName);
    }
    
    public function isBelongToUser($userId)
    {
        $userIdFieldName = self::getFieldName('user_id');
        return (int) $this->$userIdFieldName === (int) $userId;
    }
}