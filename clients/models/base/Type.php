<?php

namespace detalika\clients\models\base;

use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;


class Type extends ActiveRecord
{
    const NAME_PRIVATE_PERSON = 'Частное лицо';
    
    const TYPE_INDIVIDUAL = 'individual';
    const TYPE_LEGAL = 'legal';
    
    private static $_privatePersonClientTypeId = null;
    public static function find()
    {
        return new \detalika\clients\models\queries\Type(self::class);
    }


    public static function tableName() 
    {
        return 'clients_types';
    }
    
    public function behaviors() 
    {
        $behaviors = parent::behaviors();
        $behaviors['timestamp'] = [
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
            self::TYPE_INDIVIDUAL => 'Физическое лицо',
            self::TYPE_LEGAL => 'Юридическое лицо',
        ];
    }
    
    public function getTypeName()
    {
        if (empty($this->type)) {
            return null;
        }
        
        return self::getTypesArray()[$this->type];
    }
    
    public function getIsIndividual()
    {
        return $this->type === self::TYPE_INDIVIDUAL;
    }
    
    public function getIsLegal()
    {
        return $this->type === self::TYPE_LEGAL;
    }
    
    public static function getPrivatePersonClientTypeId()
    {
        if (self::$_privatePersonClientTypeId === null) {
            self::$_privatePersonClientTypeId = (int) static::find()
                ->select('id')
                ->where(['name' => self::NAME_PRIVATE_PERSON])
                ->scalar();
        }
        
        return self::$_privatePersonClientTypeId;
    }
    
    public static function getItemsList()
    {
        $types = static::find()
            ->select(['id', 'name'])
            ->orderBy('id ASC')    
            ->asArray()
            ->all();
        
        return ArrayHelper::map($types, 'id', 'name');
    } 
}