<?php

namespace detalika\clients\models\base;

use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;


class ContactType extends ActiveRecord
{
    const TYPE_EMAIL = 'email';
    const TYPE_PHONE = 'phone';

    public static function find()
    {
        return new \detalika\clients\models\queries\ContactType(self::class);
    }


    public static function tableName() 
    {
        return 'clients_contacts_types';
    }
    
    public function behaviors() 
    {
        $behaviors = parent::behaviors();
        $behaviors ['timestamp'] = [
            'class' => TimestampBehavior::className(),
            'createdAtAttribute' => 'created',
            'updatedAtAttribute' => 'updated',
            'value' => (new Expression('NOW()')),
        ];
        
        return $behaviors;
    }
    
    public function rules()
    {
        return [
            ['name', 'required'],
            ['name', 'string', 'max' => 255],
            ['name', 'unique'],
            
            ['type', 'required'],
            ['type', 'in', 'range' => array_keys(self::getTypesArray())],
        ];
    }
    
    public function attributeLabels() 
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'type' => 'Тип',
        ];
    }
    
    public static function getTypesArray()
    {
        return [
            self::TYPE_EMAIL => 'Email',
            self::TYPE_PHONE => 'Телефон',
        ];
    }
    
    public function getTypeName()
    {
        if (empty($this->type)) {
            return null;
        }
        
        return self::getTypesArray()[$this->type];
    }
    
    public function getIsEmail()
    {
        return $this->type === self::TYPE_EMAIL;
    }
    
    public function getIsPhone()
    {
        return $this->type === self::TYPE_PHONE;
    }
    
    public static function getItemsList()
    {
        $contactTypes = static::find()
            ->select(['id', 'name'])
            ->orderBy('id ASC')
            ->asArray()
            ->all();
        
        return ArrayHelper::map($contactTypes, 'id', 'name');
    }
}

