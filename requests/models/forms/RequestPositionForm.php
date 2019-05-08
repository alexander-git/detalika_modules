<?php

namespace detalika\requests\models\forms;

use yii\helpers\Url;
use yii\helpers\Html;
use yii\web\JsExpression;

use kartik\detail\DetailView;

use detalika\requests\OuterRoutes;
use detalika\requests\common\CommonUrls;
use detalika\requests\common\CurrentUser;
use detalika\requests\common\AccessCheck;
use detalika\requests\helpers\Select2Helper;
use detalika\requests\helpers\StandartAttributesTrait;
use detalika\requests\models\RequestPosition;
use detalika\requests\models\relation\RequestMessageInRequestPositionSearch;
use detalika\picking\widgets\RequestPositionPickingButton;

class RequestPositionForm extends RequestPosition
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
     * @return \yii\db\ActiveQuery
     */
    public function getRequestMessagesInRequestPosition()
    {
        return $this->hasMany(RequestMessageInRequestPositionSearch::className(), [
            'requests_request_position_id' => 'id',
            // Чтобы при создании нового сообщения форма правильно заполнялась
            // начальными значениями. 
            'requests_request_id' => 'requests_request_id',
        ]);
    }
    
    public function getFormFields()  
    {
        $isCreate = $this->isNewRecord;
        $canCurrentUserPicking = AccessCheck::instance()->canCurrentUserPicking();
        // Для работы замыкания.
        $model = $this;
        
        $requestsUrl = CommonUrls::getRequestsUrlForAjaxList();
        $requestPositionStatusesUrl = CommonUrls::getRequestPositionStatuesUrlForAjaxList();
        $parentIdUrl = CommonUrls::getRequestPositionParentSearchUrlForAjaxList();
        $detailsArticleUrl = Url::to(OuterRoutes::getRoute('detailsArticles'));
        $goodsUrl = Url::to(OuterRoutes::getRoute('goods'));

        $requestIdValue = '';
        if (!empty($this->request)) {
            $requestIdValue = $this->requests_request_id; 
        }

        $requestPositionStatusName = '';
        if (!empty($this->requestPositionStatusName)) {
            $requestPositionStatusName = $this->requestPositionStatusName;
        }
        
        $articleName = '';
        if (!empty($this->article)) {
            $articleName = $this->articleName;
        }
        
        $goodNameOuter = '';
        if (!empty($this->goodNameOuter)) {
            $goodNameOuter  = $this->goodNameOuter;
        }
        
        $parentIdValue = '';
        if (!empty($this->parentPositionName)) {
            $parentIdValue = $this->parentPositionName;
        }
        
        $fields = [
            'id' => [
                'attribute' => 'id',
                'type' => DetailView::INPUT_HIDDEN,
                'options' => [
                    'id' => $this->getIdHiddenInputId(),
                ],
            ],     
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
            'name' => [
                'attribute' => 'name',
            ],
            'goods_article_id' => $this->getSelect2AjaxField('goods_article_id', $detailsArticleUrl, $articleName),
            'goods_good_id' => $this->getSelect2AjaxField('goods_good_id', $goodsUrl, $goodNameOuter),            
            'good_name' => [
                'attribute' => 'good_name',
                'displayOnly' => true,
            ],            
            'price' => [
                'attribute' => 'price',
                'displayOnly' => true,
            ],
            'quantity' => [
                'attribute' => 'quantity',
                'displayOnly' => true,
            ],
            
            'delivery_partner_id' => [
                'attribute' => 'delivery_partner_id',
                'displayOnly' => true,
                'value' => function($form, $widget) {
                    return $widget->model->deliveryPartnerName;
                },
            ],    
            'link_to_search' => [
                'attribute' => 'link_to_search',
                'format' => 'raw',
                'value' => function($form, $widget) {
                    $url = $widget->model->link_to_search;
                    if (empty($url)) {
                        return '';
                    }
                    
                    return Html::a($url, $url, ['target' => '_blank']);
                },
            ],
            'requests_request_position_status_id' => $this->getSelect2AjaxField(
                'requests_request_position_status_id', 
                $requestPositionStatusesUrl, 
                $requestPositionStatusName
            ),                      
            'parent_id' => [
                'attribute' => 'parent_id',
                'type' => DetailView::INPUT_SELECT2,
                'value' => $parentIdValue,
                'widgetOptions' => [
                    'disabled' => empty($this->request),
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
            'requestMessagesCount' => [
                'attribute' => 'requestMessagesCount',
                'displayOnly' => true,
            ],
            'visible' => $this->getVisibleField(),
            'created' => $this->getCreatedField(),
            'updated' => $this->getUpdatedField(),  
        ];
                
        if (!$isCreate && $canCurrentUserPicking) {
            $fields['pickingRequestPositionUser'] = [
                'attribute' => 'pickingRequestPositionUser',
                'label' => 'Подбор',
                'format' => 'raw',
                'displayOnly' => true,
                'value' => function() use ($model) {
                    $isPickingOn = CurrentUser::instance()->isPickingRequestPosition($model);
                    
                    return RequestPositionPickingButton::widget([
                        'requestPositionId' => $model->id,
                        'isPickingOn' => $isPickingOn,
                    ]);
                },     
            ];    
        }
        
        return $fields;
    }     
    
    private function getIdHiddenInputId()
    {
        return 'idField';
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
        $idHiddenInputId = $this->getIdHiddenInputId();
        $idFieldName = 'id';
        $requestIdFieldName = 'requests_request_id';
        
        return (new JsExpression(<<<JS
function(params) {
    // Получим значение запроса, для которого нужно запросить.
    var idValue = $('#$idHiddenInputId').val();
    var requestIdValue = $('#$requestIdSelectId').val(); 
    var result = {
        'term' : params.term,
    };
    if (idValue !== '' && idValue !== null) {
        result['$idFieldName'] = idValue;
    }
    if (requestIdValue !== '' && requestIdValue !== null) {
        result['$requestIdFieldName'] = requestIdValue;
    }
                         
    return result;
}
JS
        ));      
    }
    
}