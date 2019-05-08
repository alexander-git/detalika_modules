<?php

namespace detalika\requests\models\outer;

use yii\db\ActiveRecord;
use detalika\requests\OuterTables;

class Article extends ActiveRecord
{  
    public static function tableName() 
    {
        return OuterTables::getTableName('detailsArticles');
    }    
    
    public static function getFieldName($fieldName)
    {
        return OuterTables::getFieldName('detailsArticles', $fieldName);
    }  
    
    public function getArticleName()
    {
        $nameField = self::getFieldName('name');
        return $this->$nameField; 
    }
}