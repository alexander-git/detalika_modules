<?php

namespace detalika\clients\models\base;

use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;


class CardType extends ActiveRecord
{
    public static function tableName() 
    {
        return 'clients_cards_types';
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
        ];
    }
    
    public function attributeLabels() 
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
        ];
    }
    
    public static function getItemsList()
    {
        $cardTypes = static::find()
            ->select(['id', 'name'])
            ->orderBy('id ASC')
            ->asArray()
            ->all();
        return ArrayHelper::map($cardTypes, 'id', 'name');
    }
}