<?php

namespace detalika\requests\models\base;

use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "requests_statuses".
 *
 * @property int $id
 * @property string $created
 * @property string $updated
 * @property string $name
 * @property bool $visible
 *
 * @property requests[] $requests
 */
class RequestStatus extends \yii\db\ActiveRecord
{  
    const TYPE_NEW = 'new';
    const TYPE_PROCESSED = 'processed';
    /*
    const NEW_STATUS_NAME = 'Новый'; 
    */
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'requests_request_statuses';
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
            ['name', 'required', 'enableClientValidation' => false],
            ['name', 'string', 'max' => 255],
            
            ['visible', 'boolean'],
            ['visible', 'default', 'value' => true],    
            
            ['type', 'required'],
            ['type', 'string', 'max' => 255],
            ['type', 'in', 'range' => array_keys(self::getTypesArray())],
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
            'type' => 'Тип',
            'visible' => 'Видимость',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRequests()
    {
        return $this->hasMany(Request::className(), ['requests_request_status_id' => 'id']);
    }    
    
    public function getTypeName()
    {
        if (empty($this->type)) {
            return null;    
        }
        
        return self::getTypesArray()[$this->type];
    }
    
    public function getIsTypeNew()
    {
        if (empty($this->type)) {
            return false;    
        }
        
        return $this->type === self::TYPE_NEW;
    }
    
    public function getIsTypeProcessed()
    {
        if (empty($this->type)) {
            return false;    
        }
        
        return $this->type === self::TYPE_PROCESSED;
    }
    
    public static function getTypesArray()
    {
        return [
            self::TYPE_NEW => 'Новый',
            self::TYPE_PROCESSED  => 'Обработанный',
        ];
    }
     
    public static function getNewRequestStatusId()
    {
        return self::getRequestStatusIdOnType(self::TYPE_NEW);
    }
    
    private static function getRequestStatusIdOnType($type)
    {
        $statusId = self::find()
            ->select(['id'])
            ->where(['type' => $type])
            ->scalar();
        
        if ($statusId === null) {
            $errorMessage = self::getStatusWithTypeNotExistErrorMessage($type);
            throw new \Exception($errorMessage); 
        }
        
        return $statusId;
    }
    
    private static function getStatusWithTypeNotExistErrorMessage($type)
    {
        $typeName = self::getTypesArray()[$type];
         return 'Статус c типом"'. $typeName .'" не существует.';
    }
    
    /*
    private static function getRequestStatusIdOnName($statusName)
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
    */
}
