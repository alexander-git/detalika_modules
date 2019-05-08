<?php

namespace detalika\clients\models\base;

use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;


class Card extends ActiveRecord
{      
    const NAME_PATTERN = '/^[0-9]{13}$/';
    
    
    public static function tableName() 
    {
        return 'clients_cards';
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
            ['name', 'match', 'pattern' => self::NAME_PATTERN, 'message' => 'Значение скидочной карты должно состоять из 13 чисел'],
            
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
            
            ['clients_cards_type_id', 'required'],
            [
                'clients_cards_type_id', 
                'exist',
                'skipOnError' => true, 
                'targetClass' => CardType::className(), 
                'targetAttribute' => ['clients_cards_type_id' => 'id']
            ],
            [
                ['clients_cards_type_id', 'clients_profile_id'],
                'unique',
                'targetAttribute' => ['clients_cards_type_id', 'clients_profile_id'],
            ],
        ];
    }
    
    public function attributeLabels() 
    {
        return [
            'id' => 'ID',
            'name' => 'Штрих-код',
            'clients_profile_id' => 'Клиент',
            'clients_cards_type_id' => 'Тип',
        ];
    }

    public function getCardType()
    {
        return $this->hasOne(CardType::className(), ['id' => 'clients_cards_type_id']);  
    }
    
    public function getProfile()
    {
        return $this->hasOne(Profile::className(), ['id' => 'clients_profile_id']);  
    }
    
    public function getTypeName()
    {
        if ($this->cardType === null) {
            return null; 
        }
        
        return $this->cardType->name;
    }
}