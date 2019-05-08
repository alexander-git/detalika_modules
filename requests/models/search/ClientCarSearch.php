<?php

namespace detalika\requests\models\search;

use yii\helpers\Html;
use yii\helpers\Url;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\grid\GridView as YiiGridView;

use kartik\field\FieldRange;

use detalika\requests\OuterRoutes;
use detalika\requests\models\ClientCar;
use detalika\requests\models\outer\CarMark;
use detalika\requests\models\outer\CarModel;
use detalika\requests\models\outer\CarModification;
use detalika\requests\models\outer\CarModificationEngine;
use detalika\requests\models\outer\ClientProfile;
use detalika\requests\models\outer\User;
use detalika\requests\helpers\DateTimeConstsInterface;
use detalika\requests\helpers\StandartAttributesTrait;

class ClientCarSearch extends ClientCar implements DateTimeConstsInterface
{
    use StandartAttributesTrait;
    
    use AdminNavigationFactoryTrait;
    
    const DATES_SEPARATOR = ' - ';
    
    public $term;
    public $clientNameFilter;
    public $manufactureYearFrom;
    public $manufactureYearTo;
    public $clientProfileId; // Используется для работы ajax.

    public function rules() 
    {
        return [  
            ['term', 'safe'],
            [
                [
                    'id', 
                    'cars_mark_id',
                    'cars_model_id',
                    'cars_modification_id',
                    'cars_modifications_engine_id',
                    'manufactureYearFrom',
                    'manufactureYearTo',
                    'clientProfileId',
                ], 
                'integer'
            ],
            
            [
                [
                    'name', 
                    'vin_number',
                    'clientNameFilter', 
                ], 
                'safe'
            ],
            
            ['visible', 'boolean'],
            [['created', 'updated'], 'safe'],
        ];
    }
    
    public function formName() 
    {
        // Чтобы ajax-запросом(например, из другого модуля) можно было
        // просто отправлять в GET параметр term, а не ClientCarForm[term] и
        // внешенему коду не будет нужна была информация о точнои названии
        // модели.
        return '';
    }

    public function getDataProvider()
    {        
        $clientCarTable = self::tableName();
        $carMarkTable = CarMark::tableName();
        $carModelTable = CarModel::tableName();
        $carModificationTable = CarModification::tableName();
        $carModificationEngineTable = CarModificationEngine::tableName();
        
        $clientsProfileTable = ClientProfile::tableName();
        $usersTable = User::tableName(); 
        
        $clientIdField = ClientProfile::getFieldName('id');
        $clientNameField = ClientProfile::getFieldName('name');
        $clientSurnameField = ClientProfile::getFieldName('surname');
        $clientPatronymicField = ClientProfile::getFieldName('patronymic');
        $usersEmailField = User::getFieldName('email'); 
       
        $carMarkNameField = CarMark::getFieldName('name');
        $carModelNameField = CarModel::getFieldName('name');
        $carModificationNameField = CarModification::getFieldName('name');
        $carModificationEngineCodeField = CarModificationEngine::getFieldName('engine_code');
        
        // Составим полные имена -  Имя_таблицы.Имя_поля
        $carMarkNameFieldFull = $carMarkTable . '.' . $carMarkNameField;
        $carModelNameFieldFull = $carModelTable . '.' . $carModelNameField;
        $carModificationNameFieldFull = $carModificationTable . '.' .$carModificationNameField;
        $carModificationEngineCodeFieldFull = $carModificationEngineTable . '.' .$carModificationEngineCodeField;
        
        $clientIdFieldFull = $clientsProfileTable . '.' .$clientIdField;
        $clientNameFieldFull = $clientsProfileTable . '.' . $clientNameField;
        $clientSurnameFieldFull = $clientsProfileTable . '.' . $clientSurnameField;
        $clientPatronymicFieldFull = $clientsProfileTable . '.' . $clientPatronymicField;
        $usersEmailFieldFull = $usersTable . '.' . $usersEmailField;  
        
        $q = self::find()
            ->joinWith([
                'clientCarProfiles',
//                'clientProfiles'
//                => function($q) {
//                    $q->joinWith('user');
//                }
//                ,
                'carMark',
                'carModel',
                'carModification',
                'carModificationEngine',
            ]);
        
        $dataProvider = new ActiveDataProvider([
            'query' => $q,
            'sort' => [
                'attributes' => [
                    'id',
                    'name',
                    'vin_number',
                    'manufacture_year',
                    'visible',
                    'created',
                    'updated',
                    'cars_mark_id' => [
                        'asc' => [$carMarkNameFieldFull => SORT_ASC],
                        'desc' => [$carMarkNameFieldFull => SORT_DESC],
                    ],
                    'cars_model_id' => [
                        'asc' => [$carModelNameFieldFull => SORT_ASC],
                        'desc' => [$carModelNameFieldFull => SORT_DESC],
                    ],
                    'cars_modification_id' => [
                        'asc' => [$carModificationNameFieldFull => SORT_ASC],
                        'desc' => [$carModificationNameFieldFull => SORT_DESC],
                    ],
                    'cars_modifications_engine_id' => [
                        'asc' => [$carModificationEngineCodeFieldFull => SORT_ASC],
                        'desc' => [$carModificationEngineCodeFieldFull => SORT_DESC],
                    ],
                    // К одной машине может относиться несколько клиентов и
                    // учитивая особенности GridView такое упорядочивание
                    // будет несколько условным. Записи будут упорядочены в
                    // алфавитном порядке, но по факту для опредения 
                    // положения строки таблицы(GridView) отоносительно других
                    // строк будет учитыватся одна запись из таблицы
                    // профилей  связанных с текушей записью(из таблицы машин 
                    // клиентов) - та которая в алфавитном порядке самая
                    // первая(при asc) или последняя(при desc). Отображаться при 
                    // этом будут все связанные записи.
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
        ]);

        $q->andFilterWhere([$clientCarTable . '.id' => $this->id])
            ->andFilterWhere([$clientCarTable . '.cars_mark_id' => $this->cars_mark_id])
            ->andFilterWhere([$clientCarTable . '.cars_model_id' => $this->cars_model_id])
            ->andFilterWhere([$clientCarTable . '.cars_modification_id' => $this->cars_modification_id])
            ->andFilterWhere([$clientCarTable . '.cars_modifications_engine_id' => $this->cars_modifications_engine_id])
            ->andFilterWhere([$clientCarTable . '.visible' => $this->visible]);
        
        $q->andFilterWhere(['ILIKE', $clientCarTable . '.name', $this->name])
           ->andFilterWhere(['ILIKE', $clientCarTable. '.vin_number', $this->vin_number]);
        

        $q->andFilterWhere(['or', 
            ['ILIKE', $clientNameFieldFull, $this->clientNameFilter],
            ['ILIKE', $clientSurnameFieldFull, $this->clientNameFilter],
            ['ILIKE', $clientPatronymicFieldFull, $this->clientNameFilter],
            ['ILIKE', $usersEmailFieldFull, $this->clientNameFilter],
        ]);
        
        //  Для поиска по term через ajax, когда результат 
        //  вовзращается, например, в Select2, которыей может быть 
        //  вообще в другом модуле.
        $q->andFilterWhere(['or', 
            ['ILIKE', $clientCarTable . '.name', $this->term],
            ['ILIKE', $clientCarTable. '.vin_number', $this->term],
            ['ILIKE', $carMarkNameFieldFull, $this->term],
            ['ILIKE', $carModelNameFieldFull, $this->term],
            ['ILIKE', $carModificationNameFieldFull, $this->term],
            ['ILIKE', $carModificationEngineCodeFieldFull, $this->term],
        ]);

        $q->andFilterWhere([$clientIdFieldFull => $this->clientProfileId]);
       
        $this->addDateIntervalConditionsToStandartAttributes(
            $q, 
            self::DATE_FORMAT, 
            self::DATES_SEPARATOR
        );
        
        $this->addManufactureYearIntervalConditions($q);
        $this->addOptionalConditionsToQuery($q);
        
        return $dataProvider;
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
        $carsMarksUrl = Url::to(OuterRoutes::getRoute('carsMarks'));
        $carsModelsUrl = Url::to(OuterRoutes::getRoute('carsModels'));
        $carsModificationsUrl = Url::to(OuterRoutes::getRoute('carsModifications'));
        $carsModificationsEnginesUrl = Url::to(OuterRoutes::getRoute('carsModificationsEngines'));
           
        $carMarkIdField = CarMark::getFieldName('id');
        $carMarkNameField = CarMark::getFieldName('name');
        $carModelIdField = CarModel::getFieldName('id');
        $carModelNameField = CarModel::getFieldName('name');
        $carModificationIdField = CarModification::getFieldName('id');
        $carModificationNameField = CarModification::getFieldName('name');
        $carModificationEngineIdField = CarModificationEngine::getFieldName('id');
        $carModificationEngineEngineCodeField = CarModificationEngine::getFieldName('engine_code');
        
        $carMarkColumn = $this->getSelect2AjaxColumn(
            'cars_mark_id',
            $carsMarksUrl, 
            CarMark::className(),
            $carMarkIdField,
            $carMarkNameField,
            'carMarkName'
        );
        
        $carModelColumn = $this->getSelect2AjaxColumn(
            'cars_model_id',
            $carsModelsUrl, 
            CarModel::className(),
            $carModelIdField,
            $carModelNameField,
            'carModelName'
        );
        
        $carModificationColumn = $this->getSelect2AjaxColumn(
            'cars_modification_id',
            $carsModificationsUrl, 
            CarModification::className(),
            $carModificationIdField,
            $carModificationNameField,
            'carModificationName'
        );
        
        $carModificationEngineColumn = $this->getSelect2AjaxColumn(
            'cars_modifications_engine_id',
            $carsModificationsEnginesUrl, 
            CarModificationEngine::className(),
            $carModificationEngineIdField,
            $carModificationEngineEngineCodeField,
            'carModificationEngineCode'
        );
        
        return [
            'id' => [
                'attribute' => 'id',
            ],
            'name' => [
                'attribute' => 'name',
            ],
            'clientNameFilter' => [
                'label' => 'Клиент', 
                'format' => 'raw',
                'attribute' => 'clientNameFilter',
                'value' =>  function($model, $key, $index, $column) { 
                    return $this->getClientsHtml($model);
                },
            ],
            'cars_mark_id' => $carMarkColumn,
            'cars_model_id' => $carModelColumn,
            'cars_modification_id' => $carModificationColumn,
            'cars_modifications_engine_id' => $carModificationEngineColumn,
            'manufacture_year' => [
                'attribute' => 'manufacture_year',
                'filter' => $this->getManufactureYearFilterHtml(),
            ],
            'vin_number' => [
                'attribute' => 'vin_number',
            ],
            'visible' => $this->getVisibleColumn(),
            'created' => $this->getCreatedColumn(self::DATE_FORMAT, self::DATE_TIME_FORMAT_YII, self::DATES_SEPARATOR),
            'updated' => $this->getUpdatedColumn(self::DATE_FORMAT, self::DATE_TIME_FORMAT_YII, self::DATES_SEPARATOR),
            'requsetsListLink' => $this->getRequestsListLinkColumn(),
            'actions' => $this->getActionColumn(),
        ];
    } 
    
    private function addManufactureYearIntervalConditions($query)
    {
        $t = self::tableName();
        $query->andFilterWhere(['>=', $t . '.manufacture_year', $this->manufactureYearFrom])
            ->andFilterWhere(['<=', $t . '.manufacture_year', $this->manufactureYearTo]);
        
        return $query;
    }
    
    private function getManufactureYearFilterHtml()
    {
        $searchModel = $this;
        $html = FieldRange::widget([
            'model' => $searchModel,
            'attribute1' => 'manufactureYearFrom',
            'attribute2' => 'manufactureYearTo',
            'separator' => ' - ',
            'template' => '{widget}',
            'options1' => ['type' => 'number'],
            'options2' => ['type' => 'number'],
        ]);    

        return $html;
    }
    
    private function getClientsHtml($clientCar) 
    {
        if (count($clientCar->clientProfiles) === 0) {
            return '';
        }
        
        $dataProvider = new ArrayDataProvider([
            'allModels' => $clientCar->clientProfiles,
        ]);
        
        $result = YiiGridView::widget([
            'dataProvider' => $dataProvider,
            'showOnEmpty' => false,
            'showHeader' => false,
            'summary' => '',
            'columns' => [
                'fullNameWithEmail',
            ],
        ]);            
        return  $result;
    }

    public function getRequestsListLinkColumn()
    {
        $navigationFactorty =  $this->getNavigationFactory();
        $routeItems = $navigationFactorty->createRouteItems();
        $navigation = $navigationFactorty->createNavigation();
        $requestControllerId = $routeItems->getRequestControllerId();
        
        return [
            'header' => false,
            'format' => 'raw',
            'value' => function($model, $key, $index, $column) use ($navigation, $requestControllerId) {
                $baseRoute = [
                    $requestControllerId . '/index', 
                    'requests_client_car_id' => $model->id
                ];
                $route = $navigation->getRouteFromClientCarExisitng($baseRoute);
                $url = Url::to($route);
                return Html::a('Запросы', $url);
            },
        ];
    }
    
    
    // Для использования в js.
    public static function getClientProfileIdFieldName()
    {
        return 'clientProfileId';
    }
}