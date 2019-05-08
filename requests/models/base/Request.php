<?php

namespace detalika\requests\models\base;

use yii\helpers\ArrayHelper;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

use lhs\Yii2SaveRelationsBehavior\SaveRelationsBehavior;
use lhs\Yii2SaveRelationsBehavior\SaveRelationsTrait;

use detalika\requests\helpers\OuterDependenciesTrait;
use detalika\requests\common\CurrentUser;
use detalika\requests\common\Mailer;
use detalika\requests\models\outer\ClientProfile;
use detalika\requests\models\outer\User;

/**
 * This is the model class for table "requests_requests".
 *
 * @property int $id
 * @property string $created
 * @property string $updated
 * @property bool $visible
 * @property string $requests_request_status_id
 * @property string $clients_profile_id
 * @property string $requests_client_car_id
 *
 * @property Article[] $articles
 * @property Good[] $goods
 * @property Message[] $messages
 * @property ClientCar $clientCar
 * @property Status $status
 * @property ClientProfile $clientProfile
 */
class Request extends \yii\db\ActiveRecord
{    
    use SaveRelationsTrait;
    use OuterDependenciesTrait;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'requests_requests';
    }

    public function beforeDelete()
    {
        if (!parent::beforeDelete()) {
            return false;
        }

        foreach ($this->requestPositions as $request) {
            if (!$request->delete()) {
                throw new \Exception();
            }
        }

        return true;
    }

    public function behaviors() 
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created',
                'updatedAtAttribute' => 'updated',
                'value' => (new Expression('NOW()')),
            ],
            'saveRelations' => [
                'class' => SaveRelationsBehavior::className(),
                'relations' => [
                    'requestPositions',
                ],
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $clientsProfileIdFieldName = ClientProfile::getFieldName('id');
        
        return [
            'visibleBoolean' => ['visible', 'boolean'],
            'visibleDefault' => ['visible', 'default', 'value' => true],
             
            'requestsRequestStatusIdRequired' => ['requests_request_status_id', 'required'],
            'requestsRequestStatusIdInteger' => ['requests_request_status_id', 'integer'],
            'requestsRequestStatusIdExist' => [
                'requests_request_status_id', 
                'exist', 
                'skipOnError' => true, 
                'targetClass' => RequestStatus::className(), 
                'targetAttribute' => ['requests_request_status_id' => 'id'],
            ],
            'requestRequestStatusIdDefault' => [
                'requests_request_status_id', 
                'default', 
                'value' => RequestStatus::getNewRequestStatusId(), 
            ],
            
            'requestsClientCarIdRequired' => ['requests_client_car_id', 'required'],
            'requestsClientCarIdInteger' => ['requests_client_car_id', 'integer'],
            'requestsClientCarIdExist' => [
                'requests_client_car_id', 
                'exist', 
                'skipOnError' => true, 
                'targetClass' => ClientCar::className(), 
                'targetAttribute' => ['requests_client_car_id' => 'id'],
            ],
            
            'clientsProfileIdRequired' => ['clients_profile_id', 'required'],
//            'clientsProfileIdInteger' => ['clients_profile_id', 'integer'],
//            'clientsProfileIdExist' => [
//                'clients_profile_id',
//                'exist',
//                'skipOnError' => true,
//                'targetClass' => ClientProfile::className(),
//                'targetAttribute' => ['clients_profile_id' => $clientsProfileIdFieldName],
//            ],
            
            // Для работы SaveRelationsBehavior.
            ['requestPositions', 'safe'],
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
            'visible' => 'Видимость',
            'requests_request_status_id' => 'Статус',
            'clients_profile_id' => 'Клиент',
            'requests_client_car_id' => 'Автомобиль клиента',
            'requestPositionsCount' => 'Количество позиций',
            'requestMessagesCount' => 'Количество сообщений',
            'requestPositions' => 'Позиции',
        ];
    }
    
    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT =>  self::OP_ALL,
        ];   
    }
    
    public function afterSave($insert, $changedAttributes) 
    {
        if ($insert && $this->needSendEmailToPickersWhenRequestCreate()) {     
            $requestId = $this->id;
            $clientProfileId = $this->clients_profile_id;
                    
            $dependencies = self::getOuterDependenciesStatic();

            $clientName = $dependencies->getClientNameForEmailText($clientProfileId);
            if ($clientName === null) {
                throw new \Exception('Клиент не найден');
            }
            
            $clientEmail = $dependencies->getClientEmailForEmailText($clientProfileId);
            $clientPhone = $dependencies->getClientPhoneForEmailText($clientProfileId);
            $pickerEmails =  $dependencies->getPickerEmails();
                        
            $mailer = new Mailer();
            foreach ($pickerEmails as $pickerEmail) {
                $isSendMailSuccess = $mailer->sendRequestCreatedEmail(
                    $pickerEmail, 
                    $requestId, 
                    $clientName, 
                    $clientEmail, 
                    $clientPhone
                );
                
                if (!$isSendMailSuccess) {
                    throw new \Exception('Ошибка при отправке почты');
                }
            }
        }
        
        if (!$insert && isset($changedAttributes['requests_request_status_id'])) {
            // Если статус изменился на обработанный, уведомим об этом 
            // пользователя.
            $oldRequestStatusId = $changedAttributes['requests_request_status_id'];
            $newRequestStatusId = $this->requests_request_status_id;

            if ($this->needSendRequestProcessedEmailToClient($oldRequestStatusId, $newRequestStatusId)) {
                $requestId = $this->id;
                $clientProfileId = $this->clients_profile_id;    
                $dependencies = self::getOuterDependenciesStatic();
                $clientEmail = $dependencies->getClientEmailForEmailNotificationWhenRequestProcessed($clientProfileId);
                if ($clientEmail !== null) {
                    $mailer = new Mailer();
                    $isSendMailSuccess = $mailer->sendRequestProcessedEmail($clientEmail, $requestId);
                    if (!$isSendMailSuccess) {
                        throw new \Exception('Ошибка при отправке почты');
                    }
                }
            }
        }
        
        parent::afterSave($insert, $changedAttributes);
    }
    
    public function getClientFullName()
    {
        if ($this->clientProfile === null) {
            return null;
        }
        
        return $this->clientProfile->fullName;
    }
    
    public function getClientFullNameWithEmail()
    {
        return null;
        if ($this->clientProfile === null && $this->user === null) {
            return null;
        }
        
        $result = '';
        if ($this->clientProfile !== null) {
            $result .= $this->clientFullName;
        }
        
        if ($this->user !== null) {
            $emailField = User::getFieldName('email');
            $email = $this->user->$emailField;
            $result .= '('. $email .')';
        }
        
        return $result;
    }
    
    public function getClientCarFullname()
    {
        if ($this->clientCar === null) {
            return null;
        }
        
        return $this->clientCar->carFullName;
    }
    
    public function getRequestStatusName()
    {
        if ($this->requestStatus === null) {
            return null;
        }
        
        return $this->requestStatus->name;
    }
    
    public function getRequestPositions()
    {
        return $this->hasMany(RequestPosition::className(), ['requests_request_id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRequestMessages()
    {
        return $this->hasMany(RequestMessage::className(), ['requests_request_id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRequestStatus()
    {
        return $this->hasOne(RequestStatus::className(), ['id' => 'requests_request_status_id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClientCar()
    {
        return $this->hasOne(ClientCar::className(), ['id' => 'requests_client_car_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClientProfile()
    {
        $idFieldName = ClientProfile::getFieldName('id');
        return $this->hasOne(ClientProfile::className(), [$idFieldName => 'clients_profile_id']);
    }
    
    public function getUser() 
    {
        $usersIdField = User::getFieldName('id');
        $profilesUserIdField = ClientProfile::getFieldName('user_id');
        
        return $this->hasOne(User::className(), [$usersIdField => $profilesUserIdField])
            ->via('clientProfile');
    }
    
    public function getRequestPositionsCount()
    {
        return $this->getRequestPositions()->count();
    }
    
    public function getRequestMessagesCount()
    {
        return $this->getRequestMessages()->count();
    }
    
    public static function isRequestBelongsToClient($requestId, $clientProfileId)
    {   
        $request = self::find()
            ->where(['id' => $requestId])
            ->one();
        
        if ($request === null) {
            return false;
        }
        
        if ((int) $request->clients_profile_id === (int) $clientProfileId) {
            return true;
        }

        return false;
    }
    
    public static function getRequestIdsForClient($clientProfileId)
    {
        return self::find()
            ->select(['id'])
            ->where(['clients_profile_id' => $clientProfileId])
            ->column();
    }
    
    private function needSendEmailToPickersWhenRequestCreate()
    {
        $currentUser = CurrentUser::instance();
        return  $currentUser->isGuest() || $currentUser->isClient();
    }
    
    private function needSendRequestProcessedEmailToClient(
        $oldRequestStatusId,
        $newRequestStatusId
    ) {
        $oldRequestStatus = RequestStatus::findOne(['id' => $oldRequestStatusId]);
        $newRequestStatus = RequestStatus::findOne(['id' => $newRequestStatusId]);
        return 
            $oldRequestStatus !== null &&
            $newRequestStatus !== null &&
            !$oldRequestStatus->isTypeProcessed &&
            $newRequestStatus->isTypeProcessed;
    }
}
