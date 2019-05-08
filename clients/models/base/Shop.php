<?php

namespace detalika\clients\models\base;

use yii\helpers\ArrayHelper;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

use detalika\clients\OuterTables;


class Shop extends ActiveRecord
{  
    public static function tableName() 
    {
        return OuterTables::getTableName('shops');
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
        $nameField = self::getFieldName('name');
        return [
            [$nameField, 'required'],
            [$nameField, 'string', 'max' => 255],
        ];
    }
    
    public function attributeLabels() 
    {
        $idField = self::getFieldName('id');
        $nameField = self::getFieldName('name');
        return [
            $idField => 'ID',
            $nameField => 'Название',
        ];
    }
    
    public static function getFieldName($fieldName)
    {
        return OuterTables::getFieldName('shops', $fieldName);
    } 
    
    public static function getItemsList()
    {
        $idField = self::getFieldName('id');
        $nameField = self::getFieldName('name');
        $shops = static::find()
            ->select([$idField, $nameField])
            ->orderBy("$nameField ASC")
            ->asArray()
            ->all();
        
        return ArrayHelper::map($shops, $idField, $nameField);
    }
}