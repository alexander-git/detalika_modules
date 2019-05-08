<?php

namespace detalika\requests\models\base;

use yii\helpers\ArrayHelper;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

use lhs\Yii2SaveRelationsBehavior\SaveRelationsBehavior;
use lhs\Yii2SaveRelationsBehavior\SaveRelationsTrait;
    
use detalika\requests\models\outer\CarMark;
use detalika\requests\models\outer\CarModel;
use detalika\requests\models\outer\CarModification;
use detalika\requests\models\outer\CarModificationEngine;
use detalika\requests\models\outer\ClientProfile;
use detalika\requests\models\outer\User;
use detalika\requests\validators\UniqueItemFieldInArrayValidator;

/**
 * This is the model class for table "requests_client_cars".
 *
 * @property int $id
 * @property string $created
 * @property string $updated
 * @property bool $visible
 * @property string $name
 * @property int $manufacture_year
 * @property string $vin_number
 * @property string $clients_profile_id
 * @property string $cars_mark_id
 * @property string $cars_model_id
 * @property string $cars_modification_id
 * @property string $cars_modifications_engine_id
 *
 * @property CarMark $carMark
 * @property CarModel $carModel
 * @property CarModification $carModification
 * @property CarModificationEngine $carModificationEngine
 * @property ClientProfile $clientProfile
 * @property Requests[] $requests
 */
class ClientCar extends \yii\db\ActiveRecord
{
    use SaveRelationsTrait;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'requests_client_cars';
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
                    'clientCarProfiles',
                ],
            ],
        ]);
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        $carMarkIdFieldName = CarMark::getFieldName('id');
        $carModelIdFieldName = CarModel::getFieldName('id');
        $carModificationIdFieldName = CarModification::getFieldName('id');
        $carModificationEngineIdFieldName = CarModificationEngine::getFieldName('id');
        
        return [
            ['visible', 'boolean'],
            ['visible', 'default', 'value' => true],
            
            ['manufacture_year', 'required'],
            ['manufacture_year', 'validateManufactureYear'],
            
            ['vin_number', 'required'],
            ['vin_number', 'string', 'length' => 17],
            
            ['name', 'string', 'max' => 255],
            ['name', 'default', 'value' => null],
            
            ['cars_mark_id', 'required'],
            ['cars_mark_id', 'integer'],
            [
                'cars_mark_id', 
                'exist', 
                'skipOnError' => true, 
                'targetClass' => CarMark::className(), 
                'targetAttribute' => ['cars_mark_id' => $carMarkIdFieldName],
            ],
            ['cars_mark_id', 'default', 'value' => null],            
                        
            ['cars_model_id', 'required'],
            ['cars_model_id', 'integer'],
            [
                'cars_model_id', 
                'exist', 
                'skipOnError' => true, 
                'targetClass' => CarModel::className(), 
                'targetAttribute' => ['cars_model_id' => $carModelIdFieldName],
            ],

            ['cars_modification_id', 'required'],
            ['cars_modification_id', 'integer'],
            [
                'cars_modification_id', 
                'exist', 
                'skipOnError' => true, 
                'targetClass' => CarModification::className(), 
                'targetAttribute' => ['cars_modification_id' => $carModificationIdFieldName]
            ],
            
            ['cars_modifications_engine_id', 'integer'],
            [
                'cars_modifications_engine_id', 
                'exist', 
                'skipOnError' => true, 
                'targetClass' => CarModificationEngine::className(), 
                'targetAttribute' => ['cars_modifications_engine_id' => $carModificationEngineIdFieldName],
            ],
                     
            ['comment', 'safe'],
            
            ['clientCarProfiles', 'required', 'enableClientValidation' => false],
            [
                'clientCarProfiles', 
                UniqueItemFieldInArrayValidator::className(),
                'fieldName' => 'clients_profile_id',
                'message' => 'Клиент не должен повторяться.'
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
            'created' => 'Создан',
            'updated' => 'Обновлён',
            'visible' => 'Видимость',
            'name' => 'Название',
            'manufacture_year' => 'Год выпуска',
            'vin_number' => 'Vin-номер',
            'cars_mark_id' => 'Марка',
            'cars_model_id' => 'Модель',
            'cars_modification_id' => 'Модификация',
            'cars_modifications_engine_id' => 'Двигатель (код)',
            'comment' => 'Комментарий',
            'clientCarProfiles' => 'Клиенты', 
            'carFullName' => 'Полное название',
         ];
    }
    
    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT =>  self::OP_ALL,
        ];   
    }
    
    public function beforeDelete()
    {
        if (!parent::beforeDelete()) {
            return false;
        }
        
        foreach ($this->clientCarProfiles as $clientCarProfile) {
            if (!$clientCarProfile->delete()) {
                throw new \Exception('Error while save client car profile');
            }
        }
        
        return true;
    }

    public function getCarMarkName()
    {
        if ($this->carMark === null) {
            return null;
        }
        
        $nameField = CarMark::getFieldName('name');
        return $this->carMark->$nameField;
    }
    
    public function getCarModelName()
    {
        if ($this->carModel === null) {
            return null;
        }
        
        $nameField = CarModel::getFieldName('name');
        return $this->carModel->$nameField;
    }
    
    public function getCarModificationName()
    {
        if ($this->carModification === null) {
            return null;
        }
        
        $nameField = CarModification::getFieldName('name');
        return $this->carModification->$nameField;
    }
    
    public function getCarModificationEngineCode()
    {
        if ($this->carModificationEngine === null) {
            return null;
        }
        $engineCodeField = CarModificationEngine::getFieldName('engine_code');
        return $this->carModificationEngine->$engineCodeField;
    }
    
    public function getCarFullName()
    {
        $name = '';
        
        if (!empty($this->carModification)) {
            $name .= $this->carModificationName;
        } else if (!empty($this->carModel)) {
            $name .= $this->carModelName;
        } else if (!empty($this->carMark)) {
            $name .= $this->carMarkName;
        } else if (!empty($this->name)) {
            $name .= $this->name;
        }

        if (!empty($this->vin_number)) {
            $name .= ' (' .$this->vin_number .')';
        }
        if (!empty($this->manufacture_year)) {
            $name .= ' - '.$this->manufacture_year;
        }
        
        return $name;
    }
    /**
     * Используется в заголовках, хлебных крошках и тому подобном.
     * @return string
     */
    public function getCarShortName()
    {
        $name = '';
        
        if (!empty($this->carModel)) {
            $name .= $this->carModelName;
        } else if (!empty($this->carMark)) {
            $name .= $this->carMarkName;
        } else if (!empty($this->name)) {
            $name .= $this->name;
        }

        return $name;
    }
    
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCarMark()
    {
        $idField = CarMark::getFieldName('id');
        return $this->hasOne(CarMark::className(), [$idField => 'cars_mark_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCarModel()
    {
        $idField = CarModel::getFieldName('id');
        return $this->hasOne(CarModel::className(), [$idField => 'cars_model_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCarModification()
    {
        $idField= CarModification::getFieldName('id');
        return $this->hasOne(CarModification::className(), [$idField => 'cars_modification_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCarModificationEngine()
    {
        $idField = CarModificationEngine::getFieldName('id');
        return $this->hasOne(CarModificationEngine::className(), [$idField => 'cars_modifications_engine_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClientCarProfiles()
    {
        return $this->hasMany(ClientCarProfile::className(), ['requests_client_car_id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClientProfiles()
    {
        $profilesIdField = ClientProfile::getFieldName('id');
        
        return $this->hasMany(ClientProfile::className(), [$profilesIdField => 'clients_profile_id'])
            ->via('clientCarProfiles');
    }
            
     /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers() 
    {
        $usersIdField = User::getFieldName('id');
        $profilesUserIdField = ClientProfile::getFieldName('user_id');
        
        return $this->hasMany(User::className(), [$usersIdField => $profilesUserIdField])
            ->via('clientProfiles');
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRequests()
    {
        return $this->hasMany(Requests::className(), ['requests_client_car_id' => 'id']);
    }

    public function validateManufactureYear($attribute, $params, $validator)
    {
        $currentYear = (int) (new \DateTime())->format('Y');
        if ($this->manufacture_year < 1940 || $this->manufacture_year > $currentYear) {
            $this->addError($attribute, "Год выпуска должен быть между 1940 и $currentYear");
        }
    }  

    public static function getClientCarIdsForClient($clientProfileId)
    {
        return ClientCarProfile::find()
            ->select(['requests_client_car_id'])
            ->where([
                'IN',
                'clients_profile_id',
                [$clientProfileId],
            ])
            ->column();
    }
}
