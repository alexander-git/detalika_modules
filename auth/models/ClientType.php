<?php

namespace detalika\auth\models;

/**
 * This is the model class for table "{{%auth_client_types}}".
 *
 * @property integer $id
 * @property string $name
 */
class ClientType extends \yii\db\ActiveRecord
{
    private static $_carServiceClientTypeId = null;
    private static $_carParkClientTypeId = null;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%auth_client_types}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['name', 'required'],
            ['name', 'string', 'max' => 255],
            ['name', 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Имя',
        ];
    }
    
    public static function getCarServiceClientTypeId()
    {
        if (self::$_carServiceClientTypeId === null) {
            self::$_carServiceClientTypeId = (int) static::find()
                ->select('id')
                ->where(['name' => 'Автосервис'])
                ->scalar();
        }
        
        return self::$_carServiceClientTypeId;
    }
    
    public static function getCarParkClientTypeId() 
    {
        if (self::$_carParkClientTypeId === null) {
            self::$_carParkClientTypeId = (int) static::find()
                ->select('id')
                ->where(['name' => 'Автопарк'])
                ->scalar();
        }
        
        return self::$_carParkClientTypeId;
    } 
}
