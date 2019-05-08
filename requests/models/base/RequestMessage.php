<?php

namespace detalika\requests\models\base;

use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

use detalika\requests\models\outer\User;

/**
 * This is the model class for table "requests_messages".
 *
 * @property int $id
 * @property string $created
 * @property string $updated
 * @property bool $visible
 * @property string $text
 * @property string $requests_request_id
 * @property string $user_id
 *
 * @property Request $request
 * @property User $user
 */
class RequestMessage extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'requests_request_messages';
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
        $userIdFieldName = User::getFieldName('id');
        
        return [
            'visibleBoolean' => ['visible', 'boolean'],
            'visibleDefault' => ['visible', 'default', 'value' => true],
            
            'textRequired' => ['text', 'required'],
            'textString' => ['text', 'string'],
            
            'userIdRequired' => ['user_id', 'required'],
            'userIdInteger' => ['user_id', 'integer'],
            'userIdExist' => [
                'user_id',
                'exist', 
                'skipOnError' => true, 
                'targetClass' => User::className(), 
                'targetAttribute' => ['user_id' => $userIdFieldName],
            ],
            
            'requestsRequestIdRequired' => ['requests_request_id', 'required'],
            'requestsRequestIdInteger' => ['requests_request_id', 'integer'],    
            'requestsRequestIdExist' => [
                'requests_request_id', 
                'exist', 
                'skipOnError' => true, 
                'targetClass' => Request::className(), 
                'targetAttribute' => ['requests_request_id' => 'id'],
            ],
            
            'requestsRequestPositionIdInteger' => ['requests_request_position_id', 'integer'],
            'requestsRequestPositionIdExist' => [
                'requests_request_position_id', 
                'exist', 
                'skipOnError' => true, 
                'targetClass' => RequestPosition::className(), 
                'targetAttribute' => ['requests_request_position_id' => 'id'],
            ],
            'requestsRequestPositionIdValidateRequestPositionId' => [
                'requests_request_position_id', 
                'validateRequestPositionId', 
                'skipOnEmpty' => true,
            ], 
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'created' => 'Создано',
            'updated' => 'Обновлёно',
            'visible' => 'Видимость',
            'text' => 'Текст',
            'requests_request_id' => 'Запрос',
            'requests_request_position_id' => 'Позиция запроса',
            'user_id' => 'Пользователь',
        ];
    }

    public function getUserLogin()
    {
        if ($this->user === null) {
            return null;
        }
        
        return $this->user->loginWithEmail;
    }
    
    public function getRequestPositionName()
    {
        if ($this->requestPosition === null) {
            return null;
        }
        
        return $this->requestPosition->positionName;
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRequest()
    {
        return $this->hasOne(Request::className(), ['id' => 'requests_request_id']);
    }
    
    public function getRequestPosition()
    {
        return $this->hasOne(RequestPosition::className(), ['id' => 'requests_request_position_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        $userIdFieldName = User::getFieldName('id');
        return $this->hasOne(User::className(), [$userIdFieldName => 'user_id']);
    }
    
    public function validateRequestPositionId($attribute, $params, $validator)
    {
        if (empty($this->requests_request_id)) {
            return;
        }
        
        $requestPositionsIds = RequestPosition::find()
            ->select(['id'])
            ->where(['requests_request_id' => $this->requests_request_id])
            ->column();
        
        $isCorrect = false;
        foreach ($requestPositionsIds as $requestPositionId) {
            if ((int) $this->requests_request_position_id === (int) $requestPositionId) {
                $isCorrect = true;
                break;
            }
        }
        
        if (!$isCorrect) {
            $this->addError(
                'requests_request_position_id',
                'Позиция запроса должна относиться к запросу связанному с сообщением.'
            );
        }
    }
    
    public static function getRequestMessageIdsForUser($userId)
    {
        return self::find()
            ->select(['id'])
            ->where(['user_id' => $userId])
            ->column();
    }
}
