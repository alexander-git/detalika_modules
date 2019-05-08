<?php

namespace detalika\requests;

interface OuterDependenciesInterface 
{
    // Должна возвращать идентифиекатор модуля который задан ему 
    // в разделе modules приложения.
    public function getModuleId();
    
    public function canCurrentUserAdmin();
    public function canCurrentUserPicking();
    public function isCurrentUserGuest();
    public function isCurrentUserClient();
    
    /**
     * Возвращает id профиля клиента взятого в "подбор" комплектовщиком. Если 
     * в данный момент нет клиента на подборе текущим комплектовщиком 
     * возвращает null.
     * @param integer $pickerId Id комплектовщика.
     * @return integer
     */
    public function getPickingClientProfileIdByPickerId($pickerId);
    
    /**
     * Возвращает id позиции запроса взятой в "подбор" комплектовщиком. Если 
     * в данный момент нет позиции запроса на подборе текущим комплектовщиком 
     * возвращает null.
     * @param integer $pickerId Id комплектовщика.
     * @return integer
     */
    public function getPickingRequestPositionIdByPickierId($pickerId);
    
    /**
     * Возвращает массив email-адрессов комплектовщиков.
     * При создании запроса клиентом или гостем будет отправляется 
     * уведомление на почту пользователям этим комплектовщикам.
     * @return array 
     */
    public function getPickerEmails();
    
    // Функции используются для формирования текст письма.
    public function getClientNameForEmailText($clientProfileId);
    public function getClientEmailForEmailText($clientProfileId);
    public function getClientPhoneForEmailText($clientProfileId);
    
    /**
     * @param integer $clientProfileId 
     */
    public function getClientEmailForEmailNotificationWhenRequestProcessed($clientProfileId);
    
    public function renderRequestPositionTableRow($model, $key, $index, $standartRowOutput, $grid);
    
    public function getCarsMarksTableName();
    public function getCarsMarksIdFieldName();
    public function getCarsMarksNameFieldName();
    
    public function getCarsModelsTableName();
    public function getCarsModelsIdFieldName();
    public function getCarsModelsNameFieldName();
    public function getCarsModelsCarsMarkIdFieldName();
    
    public function getCarsModificationsTableName();
    public function getCarsModificationsIdFieldName();
    public function getCarsModificationsNameFieldName();
    public function getCarsModificationsCarsModelIdFieldName();
    
    public function getCarsModificationsEnginesTableName();
    public function getCarsModificationsEnginesIdFieldName();
    public function getCarsModificationsEnginesEngineCodeFieldName();
    public function getCarsModificationsEnginesCarsModificationIdFieldName();
    
    public function getProfielsTableName();
    public function getProfielsIdFieldName();
    public function getProfielsNameFieldName();
    public function getProfielsSurnameFieldName();
    public function getProfielsPatronymicFieldName();
    public function getProfilesUserIdFieldName();
    
    public function getUsersTableName();
    public function getUsersIdFieldName();
    public function getUsersLoginFieldName();
    public function getUsersEmailFieldName();
    
    public function getGoodsTableName();
    public function getGoodsIdFieldName();
    public function getGoodsNameFieldName();
    public function getGoodsPriceFieldName();
    public function getGoodsCountFieldName(); 
    public function getGoodsDeliveryPartnerIdFieldName(); 
    
    public function getDetailsArticlesTableName();
    public function getDetailsArticlesIdFieldName();
    public function getDetailsArticlesNameFieldName();

    public function getDeliveryPartnersTableName();
    public function getDeliveryPartnersIdFieldName();
    public function getDeliveryPartnersNameFieldName();
    
    public function getPickingRequestPositionUsersTableName();
    public function getPickingRequestPositionUsersRequestPositionIdFieldName();
    public function getPickingRequestPositionUsersUserIdFieldName();
    
    public function getCarsMarksAjaxRoute();
    public function getCarsModelsAjaxRoute();
    public function getCarsModificationsAjaxRoute();
    public function getCarsModificationsEnginesAjaxRoute();
    public function getClientsProfilesAjaxRoute();
    public function getUsersAjaxRoute();
    public function getGoodsAjaxRoute();
    public function getDetailsArticlesAjaxRoute();
    public function getDeliveryPartnersAjaxRoute();
}