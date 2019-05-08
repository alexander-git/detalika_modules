<?php

namespace detalika\requests\models\forms;

use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;

use kartik\detail\DetailView;
use kartik\select2\Select2;
use unclead\multipleinput\MultipleInput;
use unclead\multipleinput\MultipleInputColumn;

use detalika\requests\helpers\StandartAttributesTrait;
use detalika\requests\helpers\Select2Helper;
use detalika\requests\OuterRoutes;
use detalika\requests\models\ClientCar;
use detalika\requests\models\outer\ClientProfile;
use detalika\requests\models\outer\CarModel;
use detalika\requests\models\outer\CarModification;
use detalika\requests\models\outer\CarModificationEngine;

class ClientCarForm extends ClientCar
{   
    use StandartAttributesTrait;
     
    public function init()
    {
        parent::init();
        
        // Чтобы  флажок для поля visible при создании записи был включён сразу.
        // При обновлении записи, поле будет переписано значением из БД.
        // После отправки формы в случае неудачной валидации в форме будет отображатся 
        // отправленное значение поля. Всё работает корректно.
        $this->visible = true;
    }

    public function getFormFields()  
    {
        $clientsProfilesUrl = Url::to(OuterRoutes::getRoute('clientsProfiles'));
        $carsMarksUrl = Url::to(OuterRoutes::getRoute('carsMarks'));
        $carsModelsUrl = Url::to(OuterRoutes::getRoute('carsModels'));
        $carsModificationsUrl = Url::to(OuterRoutes::getRoute('carsModifications'));
        $carsModificationsEnginesUrl = Url::to(OuterRoutes::getRoute('carsModificationsEngines'));
           
        $carMarkName = '';
        if (!empty($this->carMarkName)) {
            $carMarkName = $this->carMarkName;
        }
        
        $carModelName = '';
        if (!empty($this->carModelName)) {
            $carModelName = $this->carModelName;
        }
        
        $carModificationName = '';
        if (!empty($this->carModificationName)) {
            $carModificationName = $this->carModificationName;
        }
        
        $carModificationEngineCode = '';
        if (!empty($this->carModificationEngineCode)) {
            $carModificationEngineCode = $this->carModificationEngineCode;
        }
        
        $params = [
            'id' => [
                'attribute' => 'id',
                'displayOnly' => true,
                'visible' => false,
            ],
            'clientCarProfiles' => [
                'attribute' => 'clientCarProfiles',
                'type' => DetailView::INPUT_WIDGET,
                'format' => 'raw',
                'value' => $this->getClientCarProfilesHtmlInViewMode(),
                'widgetOptions' => [
                    'class' => MultipleInput::className(),
                    'allowEmptyList'    => true,
                    'addButtonPosition' => MultipleInput::POS_HEADER, 
                    'columns' => [
                        [
                            'name' => 'id',
                            'type' => MultipleInputColumn::TYPE_HIDDEN_INPUT,
                            'defaultValue' => null,
                        ],
                        [
                            'name'  => 'clients_profile_id',
                            'type'  => Select2::className(),
                            'enableError' => true,
                            'options' => [
                                // Вместо initValueText для правильного 
                                // отображения задаём data.
                                'data' => $this->getDataForClientsProfileIdFieldInMultipleInput(),
                                'options' => [
                                    'placeholder' => '',
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true,
                                    'ajax' => [
                                        'url' => $clientsProfilesUrl,
                                        'dataType' => 'json',
                                        'data' => Select2Helper::getStandartAjaxDataJs(),
                                    ],
                                    
                                ],
                            ],
                        ],
                        [
                            'name' => 'visible',
                            'type' => MultipleInputColumn::TYPE_HIDDEN_INPUT,
                            'defaultValue' => true,
                        ],
                    ],
                ],   
            ],

            'vin_number' => [
                'attribute' => 'vin_number',
            ],
            'name' => [
                'attribute' => 'name',
            ],
            'cars_mark_id' => [
                'attribute' => 'cars_mark_id',
                'type' => DetailView::INPUT_SELECT2,
                'value' => $carMarkName,
                'widgetOptions' => [
                    'options' => [
                        'id' => $this->getCarMarkSelectId(),
                        'placeholder' => '',
                    ],
                    'initValueText' =>  $carMarkName,
                    'pluginOptions' => [
                        'allowClear' => true,
                        'ajax' => [
                            'url' => $carsMarksUrl,
                            'dataType' => 'json',
                            'data' => $this->getCarMarkDataJs(),
//                            'data' => Select2Helper::getStandartAjaxDataJs(),
                        ],
                    ],
                    'pluginEvents' => [
                        'change' => $this->getCarMarkChangeJS(),
                    ],
                ],
            ],   
            
            'cars_model_id' => [
                'attribute' => 'cars_model_id',
                'type' => DetailView::INPUT_SELECT2,
                'value' => $carModelName,
                'widgetOptions' => [
                    'disabled' => empty($this->carModel),
                    'options' => [
                        'id' => $this->getCarModelSelectId(),
                        'placeholder' => ''
                    ],
                    'initValueText' => $carModelName,
                    'pluginOptions' => [
                        'allowClear' => true,
                        'ajax' => [
                            'url' => $carsModelsUrl,
                            'dataType' => 'json',
                            'data' => $this->getCarModelDataJs(),
                        ],
                    ],
                    'pluginEvents' => [
                        'change' => $this->getCarModelChangeJS(),
                    ],
                ],
            ],
            
            'cars_modification_id' => [
                'attribute' => 'cars_modification_id',
                'type' => DetailView::INPUT_SELECT2,
                'value' => $carModificationName,
                'widgetOptions' => [
                    'disabled' => empty($this->carModification),
                    'options' => [
                        'id' => $this->getCarModificationSelectId(),
                        'placeholder' => '',
                    ],
                    'initValueText' => $carModificationName,
                    'pluginOptions' => [
                        'allowClear' => true,
                        'ajax' => [
                            'url' => $carsModificationsUrl,
                            'dataType' => 'json',
                            'data' => $this->getCarModificationDataJs(),
                        ],
                    ],
                    'pluginEvents' => [
                        'change' => $this->getCarModificationChangeJS(),
                    ],
                ],
            ],
           
            'cars_modifications_engine_id' => [
                'attribute' => 'cars_modifications_engine_id',
                'type' => DetailView::INPUT_SELECT2,
                'value' => $carModificationEngineCode,
                'widgetOptions' => [
                    'disabled' => empty($this->carModificationEngine),
                    'options' => [
                        'id' => $this->getCarModificationEngineSelectId(),
                        'placeholder' => '',
                    ],
                    'initValueText' => $carModificationEngineCode,
                    'pluginOptions' => [
                        'allowClear' => true,
                        'ajax' => [
                            'url' => $carsModificationsEnginesUrl,
                            'dataType' => 'json',
                            'data' => $this->getCarModificationEngineDataJs(),
                        ],
                    ],
                ],
            ],
            
            'manufacture_year' => [
                'attribute' => 'manufacture_year',
            ],
            'comment' => [
                'attribute' => 'comment',
                'type' => DetailView::INPUT_TEXTAREA,
            ],
            'visible' => $this->getVisibleField(),
        ];

        if (!$this->isNewRecord) {
            $params = array_merge($params, [
                'created' => $this->getCreatedField(),
                'updated' => $this->getUpdatedField(),
            ]);
        }

        return $params;
    }     
    
    private function getCarMarkSelectId()
    {
        return 'carMarkSelect';
    }
    
    private function getCarModelSelectId()
    {
        return 'carModelSelect';
    }
    
    private function getCarModificationSelectId()
    {
        return 'carModificationSelect';
    }
    
    private function getCarModificationEngineSelectId()
    {
        return 'carModificationEngineSelect';
    }

    private function getCarMarkDataJs()
    {
        return (new JsExpression(<<<JS
function(params) { 
    var result = {
        'Mark': {
            'name': params.term
        },
    };

    return result;
}
JS
        ));
    }
            
    private function getCarModelDataJs()
    {
        $carMarkSelectId = $this->getCarMarkSelectId();
        $carsModelCarMarkIdFieldName = CarModel::getFieldName('cars_mark_id');
        
        return (new JsExpression(<<<JS
function(params) {
    // Получим значение марки, для которой нужно запросить модели.
    var carMarkId = $('#$carMarkSelectId').val(); 
    var result = {
        'Model': {
            'name': params.term
        },
    };
    if (carMarkId !== '' && carMarkId !== null) {
        result['Model']['$carsModelCarMarkIdFieldName'] = carMarkId;
    }
                         
    return result;
}
JS
        ));      
    }
    
    private function getCarModificationDataJs()
    {
        $carModelSelectId = $this->getCarModelSelectId();
        $carsModifcationCarModelIdFieldName = CarModification::getFieldName('cars_model_id');
        
        return (new JsExpression(<<<JS
function(params) {
    // Получим значение модели, для которой нужно запросить модификации.
    var carModelId = $('#$carModelSelectId').val(); 
    var result = {
        'Modification': {
            'name': params.term
        },
    };
    if (carModelId !== '' && carModelId !== null) {
        result['Modification']['$carsModifcationCarModelIdFieldName'] = carModelId;
    }
                         
    return result;
}
JS
        ));      
    }
    
    private function getCarModificationEngineDataJs()
    {
        $carModifciationSelectId = $this->getCarModificationSelectId();
        $carsModifcationEngineCarModificationIdFieldName = CarModificationEngine::getFieldName('cars_modification_id');
        
        return (new JsExpression(<<<JS
function(params) {
    // Получим значение модификации, для которой нужно запросить коды двигателей.
    var carModificationId = $('#$carModifciationSelectId').val(); 
    var result = {
        'ModificationsEngine': {
            'name': params.term
        },
    };
    if (carModificationId !== '' && carModificationId !== null) {
        result['ModificationsEngine']['$carsModifcationEngineCarModificationIdFieldName'] = carModificationId;
    }
                         
    return result;
}
JS
        ));      
    }
    
    private function getCarMarkChangeJs()
    {
        $carModelSelectId = $this->getCarModelSelectId();
        
        return (new JsExpression(<<<JS
function() {
    var disabled = true;         
    var carMarkIdValue = $(this).val();
    if (carMarkIdValue !== '' && carMarkIdValue !== null) {
        disabled = false;
    }
    $('#$carModelSelectId').attr('disabled', disabled);
    $('#$carModelSelectId').val(null).trigger('change');
}
JS
        ));     
    }
    
    private function getCarModelChangeJs()
    {
        $carModificationSelectId = $this->getCarModificationSelectId();
        
        return (new JsExpression(<<<JS
function() {
    var disabled = true;         
    var carModelIdValue = $(this).val();
    if (carModelIdValue !== '' && carModelIdValue !== null) {
        disabled = false;
    }
    $('#$carModificationSelectId').attr('disabled', disabled);
    $('#$carModificationSelectId').val(null).trigger('change');            
}
JS
        ));     
    }
    
    private function getCarModificationChangeJs()
    {
        $carModificationEngineSelectId = $this->getCarModificationEngineSelectId();
        
        return (new JsExpression(<<<JS
function() {
    var disabled = true;         
    var carModificationIdValue = $(this).val();
    if (carModificationIdValue !== '' && carModificationIdValue !== null) {
        disabled = false;
    }
    $('#$carModificationEngineSelectId').attr('disabled', disabled);
    $('#$carModificationEngineSelectId').val(null).trigger('change');
}
JS
        ));     
    }
    
    private function getClientCarProfilesHtmlInViewMode() 
    {
        return function () {
            $clientCarProfiles = $this->getClientCarProfiles()
                ->with(['clientProfile.user'])
                ->all();
            $dataProvider = new ArrayDataProvider([
                'allModels' => $clientCarProfiles,
            ]);

            $result = GridView::widget([
                'dataProvider' => $dataProvider,
                'showOnEmpty' => false,
                'summary' => '',
                'showHeader' => false,
                'columns' => [
                    [
                        'label' => '', // Не нужно так как showHeader - false.
                        'attribute' => "clientProfile.fullNameWithEmail",
                    ],
                ],
            ]);

            $result .= $this->getStubForMultipleInputCorrectWork('clientCarProfiles');

            return $result;
        };
    }
    
    private function getDataForClientsProfileIdFieldInMultipleInput() 
    {
        return function () {
            // Результат используется чтобы правильно отобразить ранее
            // созданные значения. Если его не задать в значении в MultipleInput
            // в select2 для clients_profile_id будет отображён id вместо имени
            // пользователя. Напрямую $this->clientProfiles не используем
            // чтобы свойство clientProfiles текущего объекта не затиралось, так как
            // может отличаться от того что физически сохранено в базе данных в
            // случае неудачной валидации после отправки формы.
            $clientProfileIds = [];
            foreach ($this->clientCarProfiles as $clientCarProfile) {
                if (!empty($clientCarProfile->clients_profile_id)) {
                    $clientProfileIds [] = $clientCarProfile->clients_profile_id;
                }
            }

            if (count($clientProfileIds) > 0) {
                $profilesIdFieldName = ClientProfile::getFieldName('id');
                $clientProfiles = ClientProfile::find()
                    ->where(['in', $profilesIdFieldName, $clientProfileIds])
                    ->all();
                $clientProfilesData = ArrayHelper::map($clientProfiles, $profilesIdFieldName, 'fullName');
            } else {
                $clientProfilesData = [];
            }

            return $clientProfilesData;
        };
    }
}