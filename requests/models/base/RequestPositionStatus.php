<?php

namespace detalika\requests\models\base;

use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "requests_request_position_statuses".
 *
 * @property int $id
 * @property string $created
 * @property string $updated
 * @property string $name
 * @property bool $visible
 *
 * @property requests[] $requests
 */
class RequestPositionStatus extends \yii\db\ActiveRecord
{
    const NEW_STATUS_NAME = 'Новая';
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'requests_request_position_statuses';
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
            
            ['visible', 'boolean'],
            ['visible', 'default', 'value' => true],
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
            'name' => 'Название',
            'visible' => 'Видимость',
        ];
    }

    public static function getNewRequestPositionStatusId()
    {
        return self::getRequestPositionStatusIdOnName(self::NEW_STATUS_NAME);
    }

    private static function getRequestPositionStatusIdOnName($statusName)
    {
        $statusId = self::find()
            ->select(['id'])
            ->where(['name' => $statusName])
            ->scalar();
        
        if ($statusId === null) {
            $errorMessage = self::getStatusNotExistErrorMessage($statusName);
            throw new \Exception($errorMessage); 
        }
        
        return $statusId;
    }
    
    private static function getStatusNotExistErrorMessage($statusName) 
    {
        return 'Статус "'. $statusName .'" не существует.';
    }
}
