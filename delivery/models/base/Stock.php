<?php

namespace detalika\delivery\models\base;

use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

use Faker\Factory as FakerFactory;

use detalika\delivery\common\InnerTables;
/**
 * This is the model class for table "delivery_stocks".
 *
 * @property integer $id
 * @property string $created
 * @property string $updated
 * @property string $name
 * @property string $address
 * @property string $work_time
 * @property string $ext_uuid
 * @property boolean $visible
 */
class Stock extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return InnerTables::getTableName('stocks');
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

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['name', 'required'],
            ['name', 'string', 'max' => 255],
            
            ['address', 'string', 'max' => 255],
            
            ['work_time', 'string', 'max' => 255],
            
            ['ext_uuid', 'string', 'max' => 255],
            ['ext_uuid', 'unique'],
            ['ext_uuid', 'default', 'value' => FakerFactory::create()->uuid],
            
            ['visible', 'boolean'],
            ['visible', 'default', 'value' => true],
            
            [['created', 'updated'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'created' => 'Создан',
            'updated' => 'Обновлён',
            'name' => 'Имя',
            'address' => 'Адрес',
            'work_time' => 'Рабочее время',
            'ext_uuid' => 'UUID',
            'visible' => 'Видимость',
        ];
    }
}
