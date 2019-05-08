<?php

namespace detalika\requests\models\outer;

use yii\db\ActiveRecord;
use detalika\requests\OuterTables;

class CarModel extends ActiveRecord
{  
    public static function tableName() 
    {
        return OuterTables::getTableName('carsModels');
    }   
    
    public static function getFieldName($fieldName)
    {
        return OuterTables::getFieldName('carsModels', $fieldName);
    }    
}