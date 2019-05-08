<?php

namespace detalika\requests;

use Yii;
use detalika\picking\PickingApi;
use detalika\clients\models\base\Profile;

class OuterDependenciesDefault implements OuterDependenciesInterface
{
    protected $_clientProfiles = [];
    
    public function getModuleId() { return 'requests'; }
    
    public function canCurrentUserAdmin() 
    {
        if (Yii::$app->user->isGuest) {
            return false;
        }
        
        //return true;
        return Yii::$app->user->can('admin');        
    }

    public function canCurrentUserPicking() 
    {
        if (Yii::$app->user->isGuest) {
            return false;
        }

        //return true;
        return Yii::$app->user->can('picking');
    }
    
    public function isCurrentUserGuest()
    {
        return Yii::$app->user->isGuest;
    }
    
    public function isCurrentUserClient()
    {
        //return true;
        return 
            !Yii::$app->user->can('picking') && 
            !Yii::$app->user->can('admin');
    }
    
    public function getPickerEmails()
    {
        return [

        ];
    }
    
    public function getClientNameForEmailText($clientProfileId)
    {
        $clientProfile = $this->getClientProfileById($clientProfileId);
        if ($clientProfile === null) {
            return null;
        }
        
        $nameParts = [];
        if (!empty($clientProfile->surname)) {
            $nameParts []= $clientProfile->surname;
        }
        if (!empty($clientProfile->name)) {
            $nameParts []= $clientProfile->name;
        }
        
        return implode(' ', $nameParts);
    }
    
    public function getClientEmailForEmailText($clientProfileId)
    {
        $clientProfile = $this->getClientProfileById($clientProfileId);
        if ($clientProfile === null) {
            return null;
        }
        // Ищем предпочитаемый контакт. Если это email, то возвращаем его. 
        // Если нет то возвращаем первый email в списке контактов.
        $email = null;
        foreach ($clientProfile->contacts as $contact) {
            if ($contact->isEmail()) {
                if ($email === null || $contact->is_main) {
                    $email = $contact->value;   
                }
            }
        }
        
        return $email;
    }
    
    public function getClientPhoneForEmailText($clientProfileId)
    {
        $clientProfile = $this->getClientProfileById($clientProfileId);
        if ($clientProfile === null) {
            return null;
        }
        
        // Ищем предпочитаемый контакт. Если это телефон, то возвращаем его. 
        // Если нет то возвращаем первый телефон в списке контактов.
        $phone = null;
        foreach ($clientProfile->contacts as $contact) {
            if ($contact->isPhone()) {
                if ($phone === null || $contact->is_main) {
                    $phone = $contact->value;   
                }
            }
        }
        
        return $phone;
    }
    
    public function getClientEmailForEmailNotificationWhenRequestProcessed($clientProfileId)
    {
        return $this->getClientEmailForEmailText($clientProfileId);
    }
    
    public function getPickingClientProfileIdByPickerId($pickerId) 
    {
        return PickingApi::getPickingClientProfileIdByPickerId($pickerId);
    }       
    
    public function getPickingRequestPositionIdByPickierId($pickerId) 
    {
        return PickingApi::getPickingRequestPositionIdByPickierId($pickerId);
    }
    
    public function renderRequestPositionTableRow($model, $key, $index, $standartRowOutput, $grid)
    {        
        /*
        // Пример использования.
        if ($model->level !== 0) {
            return '<tr><td>Нестандартный вывод</td></tr>';
        }
        */
        
        return $standartRowOutput;
    }
             
    // CarsMarks
    ////////////////////////////////////////////////////////////////////////////
    public function getCarsMarksTableName() {return 'cars_marks';}
    public function getCarsMarksIdFieldName() {return 'id';}
    public function getCarsMarksNameFieldName() {return 'name';}
    
    // CarsModels
    ////////////////////////////////////////////////////////////////////////////
    public function getCarsModelsTableName() {return 'cars_models';}
    public function getCarsModelsIdFieldName() {return 'id';}
    public function getCarsModelsNameFieldName() {return 'name';}
    public function getCarsModelsCarsMarkIdFieldName() {return 'cars_mark_id';}
    
    // CarsModifications
    ////////////////////////////////////////////////////////////////////////////
    public function getCarsModificationsTableName() {return 'cars_modifications';}
    public function getCarsModificationsIdFieldName() {return 'id';}
    public function getCarsModificationsNameFieldName() {return 'name';}
    public function getCarsModificationsCarsModelIdFieldName() {return 'cars_model_id';}
   
    // CarsModificationsEngines
    ////////////////////////////////////////////////////////////////////////////
    public function getCarsModificationsEnginesTableName() {return 'cars_modifications_engines';}
    public function getCarsModificationsEnginesIdFieldName() {return 'id';}
    public function getCarsModificationsEnginesEngineCodeFieldName() { return 'engine_code';}
    public function getCarsModificationsEnginesCarsModificationIdFieldName() {return 'cars_modification_id';}
    
    // Profile
    ////////////////////////////////////////////////////////////////////////////
    public function getProfielsTableName() {return 'clients_profiles';}
    public function getProfielsIdFieldName() {return 'id';}
    public function getProfielsNameFieldName() {return 'name';}
    public function getProfielsSurnameFieldName() {return 'surname';}
    public function getProfielsPatronymicFieldName() {return 'patronymic';}
    public function getProfilesUserIdFieldName() {return 'user_id';}
    
    // Users
    ////////////////////////////////////////////////////////////////////////////
    public function getUsersTableName() {return 'user';}
    public function getUsersIdFieldName() {return 'id';}
    public function getUsersLoginFieldName() {return 'username';}
    public function getUsersEmailFieldName() {return 'email';}
    
    // UsersGoods
    ////////////////////////////////////////////////////////////////////////////
    public function getGoodsTableName() {return 'goods_goods';}
    public function getGoodsIdFieldName() {return 'id';}
    public function getGoodsNameFieldName() {return 'name';}
    public function getGoodsPriceFieldName() {return 'price';}
    public function getGoodsCountFieldName() {return 'count';}
    public function getGoodsDeliveryPartnerIdFieldName() {return 'delivery_partner_id';}
    
    // DetailsArticles
    ////////////////////////////////////////////////////////////////////////////
    public function getDetailsArticlesTableName() {'goods_details_articles';}
    public function getDetailsArticlesIdFieldName() {return 'id';}
    public function getDetailsArticlesNameFieldName() {return 'article';}
    
    // DeliveryPartners
    ////////////////////////////////////////////////////////////////////////////
    public function getDeliveryPartnersTableName() {return 'delivery_partners';}
    public function getDeliveryPartnersIdFieldName() {return 'id';}
    public function getDeliveryPartnersNameFieldName() {return 'name';}
    
    
    // PickingRequestPositionUsers
    ////////////////////////////////////////////////////////////////////////////     
    public function getPickingRequestPositionUsersTableName() {return 'picking_request_position_users';}
    public function getPickingRequestPositionUsersRequestPositionIdFieldName() { return 'requests_request_position_id';}
    public function getPickingRequestPositionUsersUserIdFieldName() {return 'user_id';}
    
    // Маршруты
    ////////////////////////////////////////////////////////////////////////////
    public function getCarsMarksAjaxRoute() 
    {
        return ['/cars/marks/index'];
    }
    
    public function getCarsModelsAjaxRoute() 
    {
        return ['/cars/models/index'];
    }
    
    public function getCarsModificationsAjaxRoute() 
    {
        return ['/cars/modifications/index'];
    }
    
    public function getCarsModificationsEnginesAjaxRoute()
    {
        return ['/cars/modifications-engines/index'];
    }
    
    public function getClientsProfilesAjaxRoute() 
    {
        return ['/clients/profile/index'];
    }
    
    public function getUsersAjaxRoute() 
    {
        return ['/auth/user/index'];
    }
    
    public function getGoodsAjaxRoute()
    {
        return ['/goods/goods/index'];
    }
    
    public function getDetailsArticlesAjaxRoute()
    {
        return ['/goods/articles/index'];
    }
    
    public function getDeliveryPartnersAjaxRoute()
    {
        return ['/delivery/delivery-partners/index'];
    }
    
    public function getClientProfileById($clientProfileId)
    {
        if (!isset($this->_clientProfiles[$clientProfileId])) {
            $this->_clientProfiles[$clientProfileId] = Profile::findOne(['id' => $clientProfileId]);
        }
        
        return $this->_clientProfiles[$clientProfileId];
    }

    public function getClinetProfileIdByUserId($userId)
    {
        $idField = ClientProfile::getFieldName('id');
        $userIdField = ClientProfile::getFieldName('user_id');
        $clientProfile = ClientProfile::findOne([$userIdField => $userId]);
        if ($clientProfile === null) {
            return null;
        } else {
            return $clientProfile->$idField;
        }
    }

    public function getUserIdByClientProfileId($clientProfileId)
    {
        $idField = ClientProfile::getFieldName('id');
        $userIdField = ClientProfile::getFieldName('user_id');
        $clientProfile = ClientProfile::findOne([$idField => $clientProfileId]);
        if ($clientProfile === null) {
            return null;
        } else {
            return $clientProfile->$userIdField;
        }
    }
}
