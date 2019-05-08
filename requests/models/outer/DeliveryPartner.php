<?php

namespace detalika\requests\models\outer;

use yii\db\ActiveRecord;
use detalika\requests\OuterTables;

class DeliveryPartner extends ActiveRecord
{  
    public static function tableName() 
    {
        return OuterTables::getTableName('deliveryPartners');
    }    
    
    public static function getFieldName($fieldName)
    {
        return OuterTables::getFieldName('deliveryPartners', $fieldName);
    }        
}