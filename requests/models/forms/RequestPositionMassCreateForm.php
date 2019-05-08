<?php

namespace detalika\requests\models\forms;

use Yii;
use yii\base\Model;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;

use kartik\detail\DetailView;
use kartik\select2\Select2;
use unclead\multipleinput\MultipleInput;
use unclead\multipleinput\MultipleInputColumn;

use detalika\requests\validators\RequiredNotEmptyArrayValidator;
use detalika\requests\OuterRoutes;
use detalika\requests\common\CommonUrls;
use detalika\requests\helpers\Select2Helper;
use detalika\requests\helpers\ModelAdminTrait;
use detalika\requests\models\base\Request;
use detalika\requests\models\base\RequestPosition;
use detalika\requests\models\outer\Good;
use detalika\requests\models\outer\Article;


class RequestPositionMassCreateForm extends Model
{
    use ModelAdminTrait;
    
    public $requests_request_id;
    public $parent_id;
    public $requestPositions = [];
 
    public function formName()
    {
        return '';
    }

    public function __construct($config = array())
    {
        parent::__construct($config);
        // Если какие либо параметры переданы в строке запроса, сразу их установим.
        $this->load(Yii::$app->request->get());
    }

    public function rules()
    {
        return [
            'requestsRequestIdReqired' => ['requests_request_id', 'required'],
            'requestsRequestIdInteger' => ['requests_request_id', 'integer'],
            'requestsRequestIdExist' => [
                'requests_request_id', 
                'exist', 
                'skipOnError' => true, 
                'targetClass' => Request::className(), 
                'targetAttribute' => ['requests_request_id' => 'id'],
            ],
            
            
            'parentIdInteger' => ['parent_id', 'integer'],
            'parenrIdExist' => [
                'parent_id',
                'exist',
                'skipOnError' => true,
                'targetClass' => RequestPosition::className(),
                'targetAttribute' => ['parent_id' => 'id'],
            ],
            
            'requestsPositionsRequired' => [
                'requestPositions',
                RequiredNotEmptyArrayValidator::className(),
            ],
            
            'requestPositionsValidateModelsIndividual' => [
                'requestPositions', 
                'validateModelsIndividual'
            ],
        ];
    }
    
    public function attributes()
    {
        return [
            'requests_request_id',
            'parent_id',
            'requestPositions',
        ];
    }
    
    public function attributeLabels()
    {
        return [
            'requests_request_id' => 'Запрос',
            'requestPositions' => 'Позиции запроса',
            'parent_id' => 'Родитель',
        ];
    }
    
    public function scenarios()
    {
        return [
            self::SCENARIO_DEFAULT => $this->attributes()
        ];
    }
        
    public function validateModelsIndividual($attribute)
    {
        $items = $this->$attribute;
        if (count($items) === 0) {
            return;
        }           
        
        $firstError = null;
        foreach ($items as $index => $row) {
            $validationModel = new RequestPosition();
            $validationModel->scenario = RequestPosition::SCENARIO_VALIDATE_INDIVIDUAL_IN_MASS_CREATE;
            $validationModel->setAttributes($row);
            if (!$validationModel->validate()) {
                $errors = $validationModel->getFirstErrors();
                foreach ($errors as $attributeName => $errorMessage) {
                    // Раскомментировать если нужно показывать ошибки для 
                    // каждого поля в каждом столбце отдельно.
                    //$key = $attribute . '[' . $index . '][' . $attributeName . ']';
                    //$this->addError($key, $errorMessage); 
                    
                    // Будем показывать только первую ошибку для всего поля. 
                    if ($firstError === null) {
                        $firstError = '#' . $index. ': '.$errorMessage;
                    }
                    
                }
            }
        }
        if ($firstError !== null) {
            $this->addError($attribute, $firstError);
        }
    }
    
    public function getFormFields()  
    {       
        $requestsUrl = CommonUrls::getRequestsUrlForAjaxList();
        $parentIdUrl = CommonUrls::getRequestPositionParentSearchUrlForAjaxList();
        $detailsArticleUrl = Url::to(OuterRoutes::getRoute('detailsArticles'));
        $goodsUrl = Url::to(OuterRoutes::getRoute('goods'));
         
        $requestIdValue = '';
        if (!empty($this->requests_request_id)) {
            $requestIdValue = $this->requests_request_id;
        }
        
        $parentIdValue = '';
        if (!empty($this->parent_id)) {
            $parentPosition = RequestPosition::findOne(['id' => $this->parent_id]);
            if ($parentPosition !== null) {
                $parentIdValue = $parentPosition->positionName;
            }
        }
            
        $formFields = [      
            'requests_request_id' => [
                'attribute' => 'requests_request_id',
                'type' => DetailView::INPUT_SELECT2,
                'value' => $requestIdValue,
                'widgetOptions' => [
                    'options' => [
                        'id' => $this->getRequestIdSelectId(),
                        'placeholder' => '',
                    ],
                    'initValueText' =>  $requestIdValue,
                    'pluginOptions' => [
                        'allowClear' => true,
                        'ajax' => [
                            'url' => $requestsUrl,
                            'dataType' => 'json',
                            'data' => Select2Helper::getStandartAjaxDataJs(),
                        ],
                    ],
                    'pluginEvents' => [
                        'change' => $this->getRequestIdChangeJS(),
                    ],
                ],
            ],  
            
            'parent_id' => [
                'attribute' => 'parent_id',
                'type' => DetailView::INPUT_SELECT2,
                'value' => $parentIdValue,
                'widgetOptions' => [
                    'disabled' => empty($requestIdValue),
                    'options' => [
                        'id' => $this->getParentIdSelectId(),
                        'placeholder' => ''
                    ],
                    'initValueText' => $parentIdValue,
                    'pluginOptions' => [
                        'allowClear' => true,
                        'ajax' => [
                            'url' => $parentIdUrl,
                            'dataType' => 'json',
                            'data' => $this->getParentIdDataJs(),
                        ],
                    ],
                ],
            ],
            
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
        
        return $formFields;
    }   
    
    public  function save()
    {
        $attributesToCopy = [
            'name',
            'goods_article_id',
            'goods_good_id',
            'link_to_search'
        ];
        
        $transaction = RequestPosition::getDb()->beginTransaction();
        try {
            foreach ($this->requestPositions as $requestPosisition) {
                $newRequestPosition = new RequestPosition();
                $newRequestPosition->requests_request_id = $this->requests_request_id;
                if (!empty($this->parent_id)) {
                    $newRequestPosition->parent_id = $this->parent_id;
                }
                
                foreach ($attributesToCopy as $attributeName) {
                    if (isset($requestPosisition[$attributeName])) {
                        $newRequestPosition->$attributeName = $requestPosisition[$attributeName];
                    }
                }
                
                if (!$newRequestPosition->save()) {
                    throw new \Exception(); 
                }
            }
            
            $transaction->commit();
            return true;
        }
        catch (\Exception $e) {
            $transaction->rollBack();
            Yii::warning($e->getMessage());
            return false;
        }
    } 
    
    private function getDataForGoodsArticleIdFieldInMultipleInput() 
    {
        // Результат используется чтобы правильно отобразить ранее 
        // созданные значения. Если его не задать в значении в MultipleInput 
        // в select2 то будет отображён id вместо названия. 
        if (empty($this->requestPositions)) {
            return [];
        }

        $articleIds = [];
        foreach ($this->requestPositions as $requestPosition) {
            if (!empty($requestPosition['goods_article_id'])) {
                $articleIds []= $requestPosition['goods_article_id'];
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
        if (empty($this->requestPositions)) {
            return [];
        }
        
        $goodIds = [];
        foreach ($this->requestPositions as $requestPosition) {
            if (!empty($requestPosition['goods_good_id'])) {
                $goodIds []= $requestPosition['goods_good_id'];
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
    
    private function getRequestPositionsHtmlInViewMode()    
    {
        $result = '';
        $result .= $this->getStubForMultipleInputCorrectWork('requestPositions');
        return  $result;
    }
    
    
    private function getRequestIdSelectId()
    {
        return 'requestIdSelect';
    }
    
    private function getParentIdSelectId()
    {
        return 'parentIdSelect';
    }
    
    private function getRequestIdChangeJs()
    {
        $parentIdSelectId = $this->getParentIdSelectId();
        
        return (new JsExpression(<<<JS
function() {
    var disabled = true;         
    var requestIdValue = $(this).val();
    if (requestIdValue !== '' && requestIdValue !== null) {
        disabled = false;
    }
    $('#$parentIdSelectId').attr('disabled', disabled);
    $('#$parentIdSelectId').val(null).trigger('change');
}
JS
        ));     
    }
    
    private function getParentIdDataJs()
    {
        $requestIdSelectId = $this->getRequestIdSelectId();
        $requestIdFieldName = 'requests_request_id';
        
        return (new JsExpression(<<<JS
function(params) {
    // Получим значение запроса, для которого нужно запросить.
    var requestIdValue = $('#$requestIdSelectId').val(); 
    var result = {
        'term' : params.term,
    };
    if (requestIdValue !== '' && requestIdValue !== null) {
        result['$requestIdFieldName'] = requestIdValue;
    }
                         
    return result;
}
JS
        ));      
    }
}