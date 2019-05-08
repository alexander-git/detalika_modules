<?php

namespace detalika\requests\models\user\forms;

use detalika\requests\common\CommonUrls;
use detalika\requests\common\CurrentUser;
use detalika\requests\models\forms\RequestMessageForm as BaseRequestMessageForm;
use detalika\requests\validators\OnlyCurrentUserRequestsValidator;

class RequestMessageForm extends BaseRequestMessageForm
{
    public function rules()
    {
        $rules = parent::rules();
        
        $rules['requestsRequestIdOnlyCurrentUserRequests'] = [
            'requests_request_id',
            OnlyCurrentUserRequestsValidator::className(),
            'skipOnEmpty' => true,
        ];
        
        return $rules;
    }
    
    public function beforeValidate()
    {
        // По-нормальному эти значения нужно при создании записи
        // устанавливать в контроллере и запрещать их загрузку через сценарий
        // Но SaveRelationsBehavior не работает со сценариями кроме 
        // 'default', поэтому установим нужные значения перед валидацией. 
        // Хотя SaveRelationsBehavior используется не везде, но в будущем 
        // оно может понадобиться. 
        // При обновлении установим старые значения, так как пользователь их
        // менять не может.
        if ($this->isNewRecord) {
            $this->visible = true;
            // При создании записи установим текущего пользователя по умолчанию.
            $this->user_id = CurrentUser::instance()->getUserId();
        } else {
            $oldModel = self::findOne(['id' => $this->id]);
            $this->visible = $oldModel->visible;
            $this->user_id = $oldModel->user_id;
        }
        
        return parent::beforeValidate();
    }

    public function getFormFields()
    {
        $requestsUrl = CommonUrls::getUserRequestsUrlForAjaxList(); 
        $requestPositionsUrl = CommonUrls::getUserRequestPositionsUrlForAjaxList();
        
        $formFields = parent::getFormFields();
        
        unset($formFields['visible']);
        unset($formFields['user_id']);
        
        $requestIdField = $formFields['requests_request_id'];
        $requestIdField['widgetOptions']['pluginOptions']['ajax']['url'] = $requestsUrl;
        
        $requestPositionIdField = $formFields['requests_request_position_id'];
        $requestPositionIdField['widgetOptions']['pluginOptions']['ajax']['url'] = $requestPositionsUrl;
           
        $formFields['requests_request_id'] = $requestIdField;
        $formFields['requests_request_position_id'] = $requestPositionIdField;        
        
        return $formFields;
    }
}