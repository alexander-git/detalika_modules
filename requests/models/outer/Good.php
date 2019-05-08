<?php

namespace detalika\requests\models\outer;

use yii\db\ActiveRecord;
use detalika\requests\OuterTables;

class Good extends ActiveRecord
{
    public static function primaryKey()
    {
        return [self::getFieldName('id')];
    }

    public static function tableName() 
    {
        return OuterTables::getTableName('goods');
    }
     
    public static function getFieldName($fieldName)
    {
        return OuterTables::getFieldName('goods', $fieldName);
    }   
    
    public static function findGood($id)
    {
        $idFileName = self::getFieldName('id');
        return static::findOne([$idFileName => $id]);
    }
}