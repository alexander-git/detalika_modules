<?php

namespace detalika\requests\models\base;

use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

use detalika\requests\models\outer\ClientProfile;

/**
 * This is the model class for table "requests_client_car_profiles".
 *
 * @property integer $id
 * @property string $created
 * @property string $updated
 * @property boolean $visible
 * @property string $requests_client_car_id
 * @property string $clients_profile_id
 *
 * @property ClientProfile $clientProfile
 * @property ClientCar $clientCar
 */
class ClientCarProfile extends \yii\db\ActiveRecord
{   
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'requests_client_car_profiles';
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
        $clientsProfileIdFieldName = ClientProfile::getFieldName('id');
         
        return [
            ['visible', 'boolean'],
            ['visible', 'default', 'value' => true],
            
            // Использование on и except у requests_client_car_id связано 
            // с особенностями работы SaveWithRelationsBahavior 
            // в модели ClientCar.
            ['requests_client_car_id', 'safe', 'on' => [self::SCENARIO_DEFAULT]],
            ['requests_client_car_id', 'required', 'except' => [self::SCENARIO_DEFAULT]],
//            ['requests_client_car_id', 'integer',  'except' => [self::SCENARIO_DEFAULT]],
            [
                'requests_client_car_id', 
                'exist', 
                'skipOnError' => true, 
                'targetClass' => ClientCar::className(), 
                'targetAttribute' => ['requests_client_car_id' => 'id'],
                'except' => [self::SCENARIO_DEFAULT],
            ],
            
            ['clients_profile_id', 'required'],
//            ['clients_profile_id', 'integer'],
//            [
//                'clients_profile_id',
//                'exist',
//                'skipOnError' => true,
//                'targetClass' => ClientProfile::className(),
//                'targetAttribute' =>
//                ['clients_profile_id' => $clientsProfileIdFieldName],
//            ],
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
            'requests_client_car_id' => 'Автомобиль клиента',
            'clients_profile_id' => 'Профиль клиента',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClientProfile()
    {
        $clientsProfileIdFieldName = ClientProfile::getFieldName('id');
        return $this->hasOne(ClientProfile::className(), [$clientsProfileIdFieldName => 'clients_profile_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClientCar()
    {
        return $this->hasOne(ClientCar::className(), ['id' => 'requests_client_car_id']);
    }
}
