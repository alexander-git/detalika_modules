<?php

namespace detalika\requests\models\outer;

use yii\db\ActiveRecord;

use detalika\requests\OuterTables;

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
    
    public function getLoginWithEmail()
    {
        $loginField = self::getFieldName('login');
        $emailField = self::getFieldName('email');
        $result = '';
        
        if (!empty($this->$loginField)) {
            $result .= $this->$loginField;
        }
        if (!empty($this->$emailField)) {
            if (!empty($this->$loginField)) {
                $result .= '(' . $this->$emailField . ')';
            } else {
                $result .= $this->$emailField;
            }
        }
        
        return $result;
    }
}