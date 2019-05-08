<?php

namespace detalika\requests\models\search;

use yii\data\ActiveDataProvider;

use detalika\requests\common\CommonUrls;
use detalika\requests\models\Request;
use detalika\requests\models\base\RequestStatus;
use detalika\requests\models\base\RequestPosition;
use detalika\requests\models\base\RequestMessage;
use detalika\requests\models\base\ClientCar;
use detalika\requests\models\outer\ClientProfile;
use detalika\requests\models\outer\User;
use detalika\requests\models\outer\CarMark;
use detalika\requests\models\outer\CarModel;
use detalika\requests\models\outer\CarModification;
use detalika\requests\models\outer\CarModificationEngine;
use detalika\requests\helpers\DateTimeConstsInterface;
use detalika\requests\helpers\StandartAttributesTrait;

class RequestSearch extends Request implements DateTimeConstsInterface
{
    use StandartAttributesTrait;
    
    const DATES_SEPARATOR = ' - ';
    
    public $term;
    public $clientNameFilter;
    public $clientCarNameFilter;
    public $requestPositionsCountFilter;
    public $requestMessagesCountFilter;
    
    public function rules() 
    {
        return [  
            [
                [
                    'id', 
                    'clients_profile_id',
                    'requests_client_car_id',
                    'requests_request_status_id',
                    'requestPositionsCountFilter',
                    'requestMessagesCountFilter',
                ], 
                'integer'
            ],
            
            [['clientNameFilter', 'clientCarNameFilter'], 'safe'],
            
            ['visible', 'boolean'],
            [['created', 'updated'], 'safe'],
            ['term', 'safe'],
        ];
    }
    
    public function formName() 
    {
        return '';
    }

    public function getDataProvider()
    {        
        $requestTable = self::tableName();
        $requestPositionTable = RequestPosition::tableName();
        $requestMessageTable = RequestMessage::tableName();
        $clientCarTable = ClientCar::tableName();
        $carMarkTable = CarMark::tableName();
        $carModelTable = CarModel::tableName();
        $carModificationTable = CarModification::tableName();
        $carModificationEngineTable = CarModificationEngine::tableName();
        $cleintsProfileTable = ClientProfile::tableName();
        $usersTable = User::tableName(); 
       
        $clientNameField = ClientProfile::getFieldName('name');
        $clientSurnameField = ClientProfile::getFieldName('surname');
        $clientPatronymicField = ClientProfile::getFieldName('patronymic');
        $usersEmailField = User::getFieldName('email');
        $carMarkNameField = CarMark::getFieldName('name');
        $carModelNameField = CarModel::getFieldName('name');
        $carModificationNameField = CarModification::getFieldName('name');
        $carModificationEngineCodeField = CarModificationEngine::getFieldName('engine_code');
        
        // Составим полные имена -  Имя_таблицы.Имя_поля
        $carNameFieldFull = $clientCarTable . '.name';
        $vinNumberFieldFull = $clientCarTable . '.vin_number';
        $manufactureYearFieldFull = $clientCarTable . '.manufacture_year';
        $carMarkNameFieldFull = $carMarkTable . '.' . $carMarkNameField;
        $carModelNameFieldFull = $carModelTable . '.' . $carModelNameField;
        $carModificationNameFieldFull = $carModificationTable . '.' .$carModificationNameField;
        $carModificationEngineCodeFieldFull = $carModificationEngineTable . '.' .$carModificationEngineCodeField;
        $clientNameFieldFull = $cleintsProfileTable . '.' . $clientNameField;
        $clientSurnameFieldFull = $cleintsProfileTable . '.' . $clientSurnameField;
        $clientPatronymicFieldFull = $cleintsProfileTable . '.' . $clientPatronymicField;
        $usersEmailFieldFull = $usersTable . '.' . $usersEmailField;  
       
        $q = self::find()
            ->select([
                $requestTable.'.*',
                $carNameFieldFull,
                $vinNumberFieldFull,
                $carMarkNameFieldFull,
                $carModelNameFieldFull,
                $carModificationNameFieldFull,
                $carModificationEngineCodeFieldFull,
//                $clientNameFieldFull,
//                $clientSurnameFieldFull,
//                $clientPatronymicFieldFull,
//                $usersEmailFieldFull,
                "COUNT($requestPositionTable.*) AS requestPositionsCount",
                "COUNT($requestMessageTable.*) AS requestMessagesCount",
            ])
            ->joinWith([
                'requestStatus',
//                'clientProfile',
//                'user',
                'requestPositions',
                'requestMessages',
                'clientCar' => function($query) {
                    $query->joinWith([
                        'carMark',
                        'carModel',
                        'carModification',
                        'carModificationEngine',
                    ]);
                },
            ]);
                
        $baseDataProviderConfig = [
            'query' => $q,
            'sort' => [
                'attributes' => [
                    'id',
                    'visible',
                    'created',
                    'updated',
                    'clients_profile_id',
                    'requests_client_car_id',
                    'requests_request_status_id',
                    'requestPositionsCountFilter' => [
                        'asc' => ['requestPositionsCount' => SORT_ASC],
                        'desc' => ['requestPositionCount' => SORT_DESC],
                    ],
                    'requestMessagesCountFilter' => [
                        'asc' => ['requestMessagesCount' => SORT_ASC],
                        'desc' => ['requestMessagesCount' => SORT_DESC],
                    ],
                    'clientCarNameFilter' => [
                        'asc' => [
                            $carModificationNameFieldFull => SORT_ASC,
                            $carModelNameFieldFull => SORT_ASC,
                            $carMarkNameFieldFull => SORT_ASC,
                            $carNameFieldFull => SORT_ASC,
                            $vinNumberFieldFull => SORT_ASC,
                            $manufactureYearFieldFull => SORT_ASC,
                            $carModificationEngineCodeFieldFull => SORT_ASC,
                        ],
                        'desc' => [
                            $carModificationNameFieldFull => SORT_DESC,
                            $carModelNameFieldFull => SORT_DESC,
                            $carMarkNameFieldFull => SORT_DESC,
                            $carNameFieldFull => SORT_DESC,
                            $vinNumberFieldFull => SORT_DESC,
                            $manufactureYearFieldFull => SORT_DESC,
                            $carModificationEngineCodeFieldFull => SORT_DESC,
                        ],
                    ],
                    'clientNameFilter' => [
                        'asc' => [
                            $clientSurnameFieldFull => SORT_ASC,
                            $clientNameFieldFull => SORT_ASC,
                            $clientPatronymicFieldFull => SORT_ASC,
                            $usersEmailFieldFull => SORT_ASC,
                        ],
                        'desc' => [
                            $clientSurnameFieldFull => SORT_DESC,
                            $clientNameFieldFull => SORT_DESC,
                            $clientPatronymicFieldFull => SORT_DESC,
                            $usersEmailFieldFull => SORT_DESC,
                        ],
                    ], 
                ],
            ], 
        ];
        
        $dataProviderConfig = $this->modifyDataProviderConfig($baseDataProviderConfig );
        $dataProvider = new ActiveDataProvider($dataProviderConfig);

        $q->andFilterWhere([$requestTable . '.id' => $this->id])
            ->andFilterWhere([$requestTable . '.requests_request_status_id' => $this->requests_request_status_id])
            ->andFilterWhere([$requestTable . '.clients_profile_id' => $this->clients_profile_id])
            ->andFilterWhere([$requestTable . '.requests_client_car_id' => $this->requests_client_car_id])
            ->andFilterWhere([$requestTable . '.visible' => $this->visible]);
            
        $q->andFilterHaving(["COUNT($requestPositionTable.*)" => $this->requestPositionsCountFilter])
            ->andFilterHaving(["COUNT($requestMessageTable.*)" => $this->requestMessagesCountFilter]);
                
        $q->andFilterWhere(['or', 
            ['ILIKE', $clientNameFieldFull, $this->clientNameFilter],
            ['ILIKE', $clientSurnameFieldFull, $this->clientNameFilter],
            ['ILIKE', $clientPatronymicFieldFull, $this->clientNameFilter],
            ['ILIKE', $usersEmailFieldFull, $this->clientNameFilter],
        ]);
        
        $q->andFilterWhere(['or', 
            ['ILIKE', $carModificationNameFieldFull, $this->clientCarNameFilter],
            ['ILIKE', $carModelNameFieldFull, $this->clientCarNameFilter],
            ['ILIKE', $carMarkNameFieldFull, $this->clientCarNameFilter],
            ['ILIKE', $carNameFieldFull, $this->clientCarNameFilter],
            ['ILIKE', $vinNumberFieldFull, $this->clientCarNameFilter],
            ['ILIKE', $carModificationEngineCodeFieldFull, $this->clientCarNameFilter],
        ]);
        
        $q->andFilterWhere([$requestTable. '.id' => $this->term]);

        $this->addDateIntervalConditionsToStandartAttributes(
            $q, 
            self::DATE_FORMAT, 
            self::DATES_SEPARATOR
        );
                 
        $this->addOptionalConditionsToQuery($q);
        
        $q->groupBy([
            $requestTable.'.id',
            $carNameFieldFull,
            $vinNumberFieldFull,
            $carMarkNameFieldFull,
            $carModelNameFieldFull,
            $carModificationNameFieldFull,
            $carModificationEngineCodeFieldFull,
//            $clientNameFieldFull,
//            $clientSurnameFieldFull,
//            $clientPatronymicFieldFull,
//            $usersEmailFieldFull,
        ]);
        
        return $dataProvider;
    }

    // Будет использоваться для переопределения в наследниках.
    protected function modifyDataProviderConfig($baseDataProvderConfig)
    {
        return $baseDataProvderConfig;
    }
    
    // Будет использоваться для переопределения в наследниках.
    protected function addOptionalConditionsToQuery($query)
    {
        
    }
    
    public function search()
    {
        return $this->getDataProvider();
    }
    
    public function getGridColumns() 
    {
        $requestStatusesUrl = CommonUrls::getRequestStatuesUrlForAjaxList();
                
        $requestStatusIdColumn = $this->getSelect2AjaxColumn(
            'requests_request_status_id',
            $requestStatusesUrl, 
            RequestStatus::className(),
            'id',
            'name',
            'requestStatusName'
        );
        
        return [
            'id' => [
                'attribute' => 'id',
            ],
            'clientNameFilter' => [
                'label' => 'Клиент', 
                'attribute' => 'clientNameFilter',
                'value' => 'clientFullNameWithEmail',
            ],
            // TOOD# переделать в ajaxList
            'clientCarNameFilter' => [
                'label' => 'Автомобиль',
                'attribute' => 'clientCarNameFilter',
                'value' => 'clientCarFullname',
            ],
            'requestPositionsCountFilter' => [
                'label' => 'Количество позиций',
                'attribute' => 'requestPositionsCountFilter',
                'value' => function($model, $key, $index, $column) {
                    if ($model->requestPositions === null) {
                        return 0;
                    }
                    
                    return count($model->requestPositions);
                },
            ],
            'requestMessagesCountFilter' => [
                'label' => 'Количество сообщений',
                'attribute' => 'requestMessagesCountFilter',
                'value' => function($model, $key, $index, $column) {
                    if ($model->requestMessages === null) {
                        return 0;
                    }
                    
                    return count($model->requestMessages);
                },
            ],           
            'requests_request_status_id' => $requestStatusIdColumn,      
            'visible' => $this->getVisibleColumn(),
            'created' => $this->getCreatedColumn(self::DATE_FORMAT, self::DATE_TIME_FORMAT_YII, self::DATES_SEPARATOR),
            'updated' => $this->getUpdatedColumn(self::DATE_FORMAT, self::DATE_TIME_FORMAT_YII, self::DATES_SEPARATOR),
            'actions' => $this->getActionColumn(),
        ];
    } 
    
    // Для использования в js.
    public static function getClientProfileIdFieldName()
    {
        return 'clients_profile_id';
    }
}