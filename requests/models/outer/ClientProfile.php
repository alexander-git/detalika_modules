<?php

namespace detalika\requests\models\outer;

use yii\db\ActiveRecord;
use detalika\requests\OuterTables;

class ClientProfile extends ActiveRecord
{  
    public function getUser()
    {
        $usersIdFieldName = User::getFieldName('id');
        $profilesUserIdFieldName = self::getFieldName('user_id');
        
        return $this->hasOne(User::className(), [
            $usersIdFieldName => $profilesUserIdFieldName
        ]);
    }
    
    public static function tableName() 
    {
        return OuterTables::getTableName('profiles');
    }
    
    public static function getFieldName($fieldName)
    {
        return OuterTables::getFieldName('profiles', $fieldName);
    }
    
    public function getFullName()
    {
        $nameField = self::getFieldName('name');
        $surnameField = self::getFieldName('surname');
        $patronymicField = self::getFieldName('patronymic');
        
        $name = '';
        $nameParts = [];
        if (!empty($this->$nameField)) {
            $nameParts []= $this->$nameField;
        }
        if (!empty($this->$surnameField)) {
            $nameParts []= $this->$surnameField;
        }
        if (!empty($this->$patronymicField)) {
            $nameParts []= $this->$patronymicField;
        }
        
        if (count($nameParts) > 0) {
            $name = implode(' ', $nameParts);
        }

        return $name;
    }
    
    public function getFullNameWithEmail()
    {
        $result = $this->fullName;
        
        if ($this->user !== null) {            
            $emailField = User::getFieldName('email');
            $email = $this->user->$emailField;
            if (!empty($result)) {
                $result .= ' ';
            }
            $result .= '('. $email .')';    
        }
        
        return $result;
    }
}
