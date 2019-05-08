<?php

namespace detalika\clients\models\base;

use yii\helpers\ArrayHelper;
use yii\db\ActiveRecord;

use detalika\clients\OuterTables;

class User extends ActiveRecord
{  
    public static function tableName() 
    {
        return OuterTables::getTableName('users');
    }
    
    public static function getFieldName($fieldName)
    {
        return OuterTables::getFieldName('users', $fieldName);
    } 
    
    public function getProfile()
    {
        $idField = self::getFieldName('id');
        return $this->hasOne(\detalika\clients\models\Profile::className(), ['user_id' => $idField]);
    }
    
    public static function getItemsList()
    {
        $idField = self::getFieldName('id');
        $emailField = self::getFieldName('email');
        $users = static::find()
            ->select([$idField, $emailField])
            ->orderBy("$emailField ASC")
            ->asArray()
            ->all();
        
        return ArrayHelper::map($users, $idField, $emailField);
    }
}