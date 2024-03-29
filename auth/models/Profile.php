<?php

namespace detalika\auth\models;

use Yii;
use yii\helpers\ArrayHelper;
use detalika\auth\models\ClientType;
use detalika\auth\models\User;

use dektrium\user\models\Profile as BaseProfile;

class Profile extends BaseProfile
{
    const SCENARIO_CLIENT_REGISTRATION = 'clientRegistration';
    const SCENARIO_UPDATE_BY_CLIENT = 'updateByClient';
    
    const NAME_PATTERN = '/^[A-Z\x{0410}-\x{042F}][A-Z\x{0410}-\x{042F}a-z\x{0430}-\x{044F}\-]+$/u';
    const SURNAME_PATTERN = '/^[A-Z\x{0410}-\x{042F}][A-Z\x{0410}-\x{042F}a-z\x{0430}-\x{044F}\-]+$/u';
    const PHONE_PATTERN = '/^\+[0-9]\([0-9]{3}\)[0-9]{3}\-[0-9]{2}\-[0-9]{2}$/';
    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub
        $this->name = '';
        $this->surname = '';
        $this->phone = '';
        $this->city = '';
    }
    
    public function rules()
    {   
        $rules = parent::rules();
        unset($rules['nameLength']);        
        return ArrayHelper::merge($rules, self::getAdditionalFieldRules());
    }
    
    public function attributeLabels() 
    {
        return ArrayHelper::merge(parent::rules(), self::getAdditionalFieldLabels());
    }
        
    
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        
        $scenarios[self::SCENARIO_CLIENT_REGISTRATION] = [
            'name',
            'surname',
            'phone',
            'city',
            'clients_type_id',
            'company_name',
        ];
        
        $scenarios[self::SCENARIO_UPDATE_BY_CLIENT] = [
            'name',
            'surname',
            'phone',
            'city',
            'clients_type_id',
            'company_name',
            'delivery_address',            
        ];
        
        return $scenarios;
    }
    
//    public function getClientType()
//    {
//        return $this->hasOne(ClientType::className(), ['id' => 'clients_type_id']);
//    }
    
    public function getFullName()
    {
        return $this->name . ' ' . $this->surname;
    }
    
    public function getIsCarServiceClientType()
    {
        if ($this->clients_type_id === null) {
            return false;
        }
        
        return $this->clients_type_id === ClientType::getCarServiceClientTypeId();
    }
    
    public function getIsCarParkClientType()
    {
        if ($this->clients_type_id === null) {
            return false;
        }
        
        return $this->clients_type_id === ClientType::getCarParkClientTypeId();
    }
    
    /**
     * Обновление профиля клиентом.
     * 
     * @param \detalika\auth\models\User $user Пользователь которому принадлежит 
     *   профиль. У него может меняется email.
     * 
     * @return bool
     */
    public function updateByClient(User $user)
    {       
        $transaction = $this->getDb()->beginTransaction();
        try {
            if (!$user->save()) {
                $transaction->rollBack();
                return false;
            }
            
            if (!$this->save()) {
                $transaction->rollBack();
                return false;
            }

            $transaction->commit();
            return true;
        } catch (\Exception $e) {
            Yii::warning($e->getMessage());
            $transaction->rollBack();
            return false;
        }
    }
    
    public static function getFieldsRulesForRegistration() 
    {
        return [
            'nameRequired' => ['name', 'required'],
            'nameLength' => ['name', 'string', 'max' => 255],
            'nameMatch' => ['name', 'match', 'pattern' => self::NAME_PATTERN],
            
//            'surnameRequired' => ['surname', 'required'],
            'surnameLength' => ['surname', 'string', 'max' => 255],
            'surnameMatch' => ['surname', 'match', 'pattern' => self::SURNAME_PATTERN],
            
            'phoneRequired' => ['phone', 'required'],
//            'phoneMatch' => ['phone', 'match', 'pattern' => self::PHONE_PATTERN],

//            'cityRequired' => ['city', 'required'],
            'cityLength' => ['city', 'string', 'max' => 255],

            'company_nameLength' => ['company_name', 'string', 'max' => 255],
            
//            'clients_type_idRequired' => ['clients_type_id', 'required'],
            'clients_type_idInteger' => ['clients_type_id', 'integer'],
            
            'clients_type_idExist' => [
                'clients_type_id',
                'exist',
                'skipOnError' => true, 
                'targetClass' => ClientType::className(), 
                'targetAttribute' => ['clients_type_id' => 'id']
            ],
        ];
    }
    
    public static function getFildsLablesForRegistration()
    {
        return [
            'name' => 'Имя',
            'surname' => 'Фамилия',
            'phone' => 'Мобил. Телефон',
            'city' => 'Город',
            'clients_type_id' => 'Тип клиента',
            'company_name' => 'Название автосервиса/автопарка',
        ];
    }
    
    private static function getAdditionalFieldRules() 
    {
        return ArrayHelper::merge(static::getFieldsRulesForRegistration(), [
           'delivery_addressLength' => ['delivery_address', 'string', 'max' => 255],
        ]);
    }
    
    private static function getAdditionalFieldLabels()
    {
        return ArrayHelper::merge(static::getFildsLablesForRegistration(), [
            'delivery_address' => 'Адрес доставки',
        ]);
    }
    
}
