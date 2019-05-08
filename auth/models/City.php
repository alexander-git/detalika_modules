<?php

namespace detalika\auth\models;

use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%auth_cities}}".
 *
 * @property string $name
 */
class City extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%auth_cities}}';
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
            'name' => 'Имя',
        ];
    }
    
    public static function getAllCitiesList()
    {
        $cities = static::find()->orderBy('name ASC')->asArray()->all();
        return ArrayHelper::map($cities, 'name', 'name');
    }
}
