<?php

namespace detalika\requests\models\user\forms;

use detalika\requests\common\CurrentUser;
use detalika\requests\models\forms\ClientCarForm as BaseClientCarForm;

class ClientCarForm extends BaseClientCarForm 
{
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
            $carProfiles = $this->getDefaultCarProfiles();
            $this->clientCarProfiles = $carProfiles;
        } else {
            $oldModel = self::findOne(['id' => $this->id]);
            $this->visible = $oldModel->visible;
            $this->clientCarProfiles = $oldModel->clientCarProfiles;
        }

        return parent::beforeValidate();
    }
    
    public function getFormFields()
    {
        $formFields = parent::getFormFields();
        
        unset($formFields['visible']);
        unset($formFields['clientCarProfiles']);
        
        return $formFields;
    }

    /**
     * @return array
     */
    protected function getDefaultCarProfiles()
    {
        $carProfiles = [
            [
                'id' => null,
                'clients_profile_id' => CurrentUser::instance()->getClientProfileId(),
            ],
        ];
        return $carProfiles;
    }
}