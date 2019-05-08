<?php

namespace detalika\requests\models\forms;

use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;

use kartik\detail\DetailView;
use kartik\select2\Select2;
use unclead\multipleinput\MultipleInput;
use unclead\multipleinput\MultipleInputColumn;

use detalika\requests\helpers\StandartAttributesTrait;
use detalika\requests\helpers\Select2Helper;
use detalika\requests\common\CommonUrls;
use detalika\requests\OuterRoutes;
use detalika\requests\models\Request;
use detalika\requests\models\outer\Good;
use detalika\requests\models\outer\Article;
use detalika\requests\models\search\ClientCarSearch;
use detalika\requests\models\relation\RequestPositionInRequestSearch;
use detalika\requests\models\relation\RequestMessageInRequestSearch;

class RequestForm extends Request
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
    

    /**
     * Назование relation-а будет requestPositionsInRequest
     * вместо requestPositions чтобы не возникало проблем с мультиформой и 
     * использованием SaveRelationsBahaviour.
     * @return \yii\db\ActiveQuery
     */
    public function getRequestPositionsInRequest()
    {
        return $this->hasMany(RequestPositionInRequestSearch::className(), ['requests_request_id' => 'id']);
    }
    
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRequestMessagesInRequest()
    {
        return $this->hasMany(RequestMessageInRequestSearch::className(), ['requests_request_id' => 'id']);
    }
        
    public function getFormFields()  
    {
        $clientCarsUrl = CommonUrls::getClientCarsUrlForAjaxList(); 
        $requestStatusesUrl = CommonUrls::getRequestStatuesUrlForAjaxList();
        $clientsProfilesUrl = Url::to(OuterRoutes::getRoute('clientsProfiles'));
        $detailsArticleUrl = Url::to(OuterRoutes::getRoute('detailsArticles'));
        $goodsUrl = Url::to(OuterRoutes::getRoute('goods'));
          
        $clientFullNameWithEmail = '';
        if (!empty($this->clientFullNameWithEmail)) {
            $clientFullNameWithEmail = $this->clientFullNameWithEmail; 
        }
        
        $clientCarFullName = '';
        if (!empty($this->clientCarFullName)) {
            $clientCarFullName = $this->clientCarFullName;
        }
        
        $requestStatusName = '';
        if (!empty($this->requestStatusName)) {
            $requestStatusName = $this->requestStatusName;
        }

        $isCreate = $this->isNewRecord;
        
        $formFields = [
            'id' => [
                'attribute' => 'id',
                'displayOnly' => true,
            ],
            'clients_profile_id' => [
                'attribute' => 'clients_profile_id',
                'type' => DetailView::INPUT_SELECT2,
                'value' => $clientFullNameWithEmail,
                'widgetOptions' => [
                    'options' => [
                        'id' => $this->getClientProfileSelectId(),
                        'placeholder' => '',
                    ],
                    'initValueText' =>  $clientFullNameWithEmail,
                    'pluginOptions' => [
                        'allowClear' => true,
                        'ajax' => [
                            'url' => $clientsProfilesUrl,
                            'dataType' => 'json',
                            'data' => Select2Helper::getStandartAjaxDataJs(),
                        ],
                    ],
                    'pluginEvents' => [
                        'change' => $this->getClientProfileChangeJS(),
                    ],   
                ],
            ],    
            'requests_client_car_id' => [
                'attribute' => 'requests_client_car_id',
                'type' => DetailView::INPUT_SELECT2,
                'value' => $clientCarFullName,
                'widgetOptions' => [
                    'disabled' => empty($this->clientCar),
                    'options' => [
                        'id' => $this->getClientCarSelectId(),
                        'placeholder' => '',
                    ],
                    'initValueText' =>  $clientCarFullName,
                    'pluginOptions' => [
                        'allowClear' => true,
                        'ajax' => [
                            'url' => $clientCarsUrl,
                            'dataType' => 'json',
                            'data' => $this->getClientCarDataJs(),
                        ],
                    ],
                ],
            ],
            'requests_request_status_id' => $this->getSelect2AjaxField(
                'requests_request_status_id',
                $requestStatusesUrl,
                $requestStatusName
            ),
                        
            'requestPositions' => [
                'attribute' => 'requestPositions',
                'type' => DetailView::INPUT_WIDGET,
                'format' => 'raw',
                'value' => $this->getRequestPositionsHtmlInViewMode(),
                'widgetOptions' => [
                    'class' => MultipleInput::className(),
                    'allowEmptyList' => false,
                    'addButtonPosition' => MultipleInput::POS_HEADER, 
                    'columns' => [
                        'id' => [
                            'name' => 'id',
                            'type' => MultipleInputColumn::TYPE_HIDDEN_INPUT,
                            'defaultValue' => null,
                        ],
                        'name' => [
                            'name' => 'name',
                            'title' => 'Название',
                            'type' => MultipleInputColumn::TYPE_TEXT_INPUT,
                        ],
                        'goods_article_id' => [
                            'name'  => 'goods_article_id',
                            'title' => 'Артикул',
                            'type'  => Select2::className(),
                            'enableError' => true,
                            'options' => [
                                // Вместо initValueText для правильного 
                                // отображения задаём data.
                               'data' => $this->getDataForGoodsArticleIdFieldInMultipleInput(),
                                'options' => [
                                    'placeholder' => '',
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true,
                                    'ajax' => [
                                        'url' => $detailsArticleUrl,
                                        'dataType' => 'json',
                                        'data' => Select2Helper::getStandartAjaxDataJs(),
                                    ],
                                    
                                ],
                            ],
                        ],
                        'goods_good_id' => [
                            'name'  => 'goods_good_id',
                            'title' => 'Товар',
                            'type'  => Select2::className(),
                            'enableError' => true,
                            'options' => [
                                // Вместо initValueText для правильного 
                                // отображения задаём data.
                                'data' => $this->getDataForGoodsGoodIdFieldInMultipleInput() ,
                                'options' => [
                                    'placeholder' => '',
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true,
                                    'ajax' => [
                                        'url' => $goodsUrl,
                                        'dataType' => 'json',
                                        'data' => Select2Helper::getStandartAjaxDataJs(),
                                    ],
                                ],
                            ],
                        ],
                        'link_to_search' => [
                            'name' => 'link_to_search',
                            'title' => 'Ссылка на поиск',
                            'type' => MultipleInputColumn::TYPE_TEXT_INPUT,
                        ],
                    ],
                ],   
            ],
        ];
        
        if ($isCreate) {
            unset($formFields['id']);
        }
        
        if (!$isCreate) {
            $formFields = ArrayHelper::merge($formFields, [
                'requestPositionsCount' => [
                    'attribute' => 'requestPositionsCount',
                    'displayOnly' => true,
                ],
                'requestMessagesCount' => [
                    'attribute' => 'requestMessagesCount',
                    'displayOnly' => true,
                ],
                'visible' => $this->getVisibleField(),
                'created' => $this->getCreatedField(),
                'updated' => $this->getUpdatedField(),
            ]);
            // Возможность добавлять позиции у мультформы только при создании.
            unset($formFields['requestPositions']);
        }
        
        return $formFields;
    }   
    
    public function getClientProfileSelectId()
    {
        return 'cleintProfileSelect';
    }
    
    public function getClientCarSelectId()
    {
        return 'clientCarSelect';
    }
    
    private function getClientCarDataJs()
    {
        $clientProfileSelectId = $this->getClientProfileSelectId();
        $cleintCarClientProfileIdFieldName = ClientCarSearch::getClientProfileIdFieldName();
        
        return (new JsExpression(<<<JS
function(params) {
    // Получим значение id клиента, для которого нужно запросить автомобили.
    var clientProfileId = $('#$clientProfileSelectId').val(); 
    var result = {
        'term' : params.term,
    };
    if (clientProfileId !== '' && clientProfileId !== null) {
        result['$cleintCarClientProfileIdFieldName'] = clientProfileId;
    }
                         
    return result;
}
JS
        ));      
    }

    private function getClientProfileChangeJs()
    {
        $clientCarSelectId = $this->getClientCarSelectId();
        
        return (new JsExpression(<<<JS
function() {
    var disabled = true;         
    var clientProfileIdValue = $(this).val();
    if (clientProfileIdValue !== '' && clientProfileIdValue !== null) {
        disabled = false;
    }
    $('#$clientCarSelectId').attr('disabled', disabled);
    $('#$clientCarSelectId').val(null).trigger('change');
}
JS
        ));     
    } 
    
          
    private function getRequestPositionsHtmlInViewMode()    
    {
        $result = '';
        $result .= $this->getStubForMultipleInputCorrectWork('requestPositions');
        return  $result;
    }
    
    private function getDataForGoodsArticleIdFieldInMultipleInput() 
    {
        // Результат используется чтобы правильно отобразить ранее 
        // созданные значения. Если его не задать в значении в MultipleInput 
        // в select2 то будет отображён id вместо названия. 
        $articleIds = [];
        foreach ($this->requestPositions as $requestPosition) {
            if (!empty($requestPosition->goods_article_id)) {
                $articleIds []= $requestPosition->goods_article_id;
            }
        }
        
        if (count($articleIds) > 0) {
            $articleIdFieldName = Article::getFieldName('id');
            $articleNameFieldName = Article::getFieldName('name');
            $articles = Article::find()
                ->where(['in', $articleIdFieldName, $articleIds])
                ->all();
            $articlesData = ArrayHelper::map($articles, $articleIdFieldName, $articleNameFieldName);
        } else {
            $articlesData = [];
        }
        
        return $articlesData;
    }
    
    private function getDataForGoodsGoodIdFieldInMultipleInput() 
    {
        // Результат используется чтобы правильно отобразить ранее 
        // созданные значения. Если его не задать в значении в MultipleInput 
        // в select2 то будет отображён id вместо названия. 
        $goodIds = [];
        foreach ($this->requestPositions as $requestPosition) {
            if (!empty($requestPosition->goods_good_id)) {
                $goodIds []= $requestPosition->goods_good_id;
            }
        }
        
        if (count($goodIds) > 0) {
            $goodIdFieldName = Good::getFieldName('id');
            $goodNameFieldName = Good::getFieldName('name');
            $goods = Good::find()
                ->where(['in',  $goodIdFieldName, $goodIds])
                ->all();
            $goodsData = ArrayHelper::map($goods, $goodIdFieldName, $goodNameFieldName);
        } else {
            $goodsData = [];
        }
        
        return $goodsData;
    }
}