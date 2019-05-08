<?php

namespace detalika\requests\models\outer;

use yii\db\ActiveRecord;
use detalika\requests\OuterTables;

class CarMark extends ActiveRecord
{  
    public static function tableName() 
    {
        return OuterTables::getTableName('carsMarks');
    }    
    
    public static function getFieldName($fieldName)
    {
        return OuterTables::getFieldName('carsMarks', $fieldName);
    }    
}