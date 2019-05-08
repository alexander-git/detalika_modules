<?php

namespace detalika\requests\models\forms;

use yii\helpers\Url;
use yii\web\JsExpression;

use kartik\detail\DetailView;

use detalika\requests\OuterRoutes;
use detalika\requests\common\CommonUrls;
use detalika\requests\helpers\Select2Helper;
use detalika\requests\helpers\StandartAttributesTrait;
use detalika\requests\models\RequestMessage;

class RequestMessageForm extends RequestMessage
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
        $requestsUrl = CommonUrls::getRequestsUrlForAjaxList(); 
        $requestPositionsUrl = CommonUrls::getRequestPositionsUrlForAjaxList();
        $usersUrl = Url::to(OuterRoutes::getRoute('users'));  
        
        $requestIdValue = '';
        if (!empty($this->request)) {
            $requestIdValue = $this->requests_request_id; 
        }
        
        $userLogin = '';
        if (!empty($this->userLogin)) {
            $userLogin = $this->userLogin;
        }
        
        $requestPositionName = '';
        if (!empty($this->requestPositionName)) {
            $requestPositionName = $this->requestPositionName;
        }
        
        return [
            'id' => [
                'attribute' => 'id',
                'displayOnly' => true,
                'visible' => !$this->isNewRecord,
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
            
            'user_id' => $this->getSelect2AjaxField(
                'user_id', 
                $usersUrl, 
                $userLogin
            ),  
            
            'requests_request_position_id' => [
                'attribute' => 'requests_request_position_id',
                'type' => DetailView::INPUT_SELECT2,
                'value' => $requestPositionName,
                'widgetOptions' => [
                    'disabled' => empty($this->request),
                    'options' => [
                        'placeholder' => '',
                        'id' => $this->getRequestPositionIdSelectId(),
                    ],
                    'initValueText' =>  $requestPositionName,
                    'pluginOptions' => [
                        'allowClear' => true,
                        'ajax' => [
                            'url' => $requestPositionsUrl,
                            'dataType' => 'json',
                            'data' => $this->getRequestPositionDataJs(),
                        ],
                    ],  
                ],
            ],    
            'text' => [
                'attribute' => 'text',
                'type' => DetailView::INPUT_TEXTAREA,
            ],
            'visible' => $this->getVisibleField(),
            'created' => $this->getCreatedField(),
            'updated' => $this->getUpdatedField(),
        ];
    }   
            
    private function getRequestIdSelectId()
    {
        return 'requestIdSelect';
    }
    
    private function getRequestPositionIdSelectId()
    {
        return 'requestPositionIdSelect';
    }
    
    private function getRequestIdChangeJs()
    {
        $requestPositionIdSelectId = $this->getRequestPositionIdSelectId();
        
        return (new JsExpression(<<<JS
function() {
    var disabled = true;         
    var requestIdValue = $(this).val();
    if (requestIdValue !== '' && requestIdValue !== null) {
        disabled = false;
    }
    $('#$requestPositionIdSelectId').attr('disabled', disabled);
    $('#$requestPositionIdSelectId').val(null).trigger('change');
}
JS
        ));     
    }
    
    private function getRequestPositionDataJs()
    {
        $requestIdSelectId = $this->getRequestIdSelectId();
        $requestIdFieldName = 'requests_request_id';
        
        return (new JsExpression(<<<JS
function(params) {
    // Получим значение запроса, для которого нужнор запросить.
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