<?php

namespace detalika\clients\models\base;

use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

class Contact extends ActiveRecord
{           
    const PHONE_PATTERN = '/^[0-9]{8,}$/';

    public static function find()
    {
        return new \detalika\clients\models\queries\Contact(self::class);
    }

    public static function tableName() 
    {
        return 'clients_contacts';
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
            ['value', 'required'],
            ['value', 'string', 'max' => 255],
            ['value', 'email', 'when' => function($model) {
                return $model->contactType !== null && $model->contactType->isEmail; 
            }], 
            ['value', 'match', 'pattern' => self::PHONE_PATTERN, 'when' => function($model) {
                return $model->contactType !== null && $model->contactType->isPhone;
            }],
            
            // Использование on и except у clients_profile_id связано 
            // с особенностями работы SaveWithRelationsBahavior 
            // в модели Profile.
            ['clients_profile_id', 'safe', 'on' => [self::SCENARIO_DEFAULT]],       
            ['clients_profile_id', 'required', 'except' => [self::SCENARIO_DEFAULT]],
            [
                'clients_profile_id',
                'exist',
                'skipOnError' => true, 
                'targetClass' => Profile::className(), 
                'targetAttribute' => ['clients_profile_id' => 'id'],
                'except' => [self::SCENARIO_DEFAULT],
            ],        
            
            ['clients_contacts_type_id', 'required'],
            [
                'clients_contacts_type_id', 
                'exist',
                'skipOnError' => true, 
                'targetClass' => ContactType::className(), 
                'targetAttribute' => ['clients_contacts_type_id' => 'id']
            ],
            [
                ['clients_contacts_type_id', 'clients_profile_id'],
                'unique',
                'targetAttribute' => ['clients_contacts_type_id', 'clients_profile_id'],
            ],
            
            ['is_main', 'boolean'],
            ['is_main', 'default', 'value' => false],  
        ];
    }
    
    public function attributeLabels() 
    {
        return [
            'id' => 'ID',
            'value' => 'Значение',
            'clients_profile_id' => 'Клиент',
            'clients_contacts_type_id' => 'Тип контакта',
            'is_main' => 'Предпочтительный',
        ];
    }

    public function getContactType()
    {
        return $this->hasOne(ContactType::className(), ['id' => 'clients_contacts_type_id']);  
    }

    public function isPhone() {
        return $this->contactType->getIsPhone();
    }
    
    public function isEmail()
    {
        return $this->contactType->getIsEmail();
    }
    
    public function getProfile()
    {
        return $this->hasOne(Profile::className(), ['id' => 'clients_profile_id']);  
    }
    
    public function getTypeName()
    {
        if ($this->contactType === null) {
            return null;
        }
        
        return $this->contactType->name;
    }

    public function isEqual($type, $value) {
        if ($contactType = $this->contactType) {
            if ($contactType->type == $type && $value == $this->value) {
                return true;
            }
        }
    }
}