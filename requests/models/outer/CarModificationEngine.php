<?php

namespace detalika\requests\models\outer;

use yii\db\ActiveRecord;
use detalika\requests\OuterTables;

class CarModificationEngine extends ActiveRecord
{  
    public static function tableName() 
    {
        return OuterTables::getTableName('carsModificationsEngines');
    }    

    public static function getFieldName($fieldName)
    {
        return OuterTables::getFieldName('carsModificationsEngines', $fieldName);
    }   
}