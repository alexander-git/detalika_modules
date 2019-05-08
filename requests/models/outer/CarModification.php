<?php

namespace detalika\requests\models\outer;

use yii\db\ActiveRecord;
use detalika\requests\OuterTables;

class CarModification extends ActiveRecord
{  
    public static function tableName() 
    {
        return OuterTables::getTableName('carsModifications');
    }    
    
    public static function getFieldName($fieldName)
    {
        return OuterTables::getFieldName('carsModifications', $fieldName);
    }   
}