<?php

namespace detalika\requests;

use detalika\requests\helpers\OuterDependenciesTrait;

class OuterTables 
{
    use OuterDependenciesTrait;
    
    private static $_tableNames = null;
    private static $_fieldNames = null;

    
    public static function getTableName($tableName)
    {
        if (isset(self::getTableNames()[$tableName])) {
            return self::getTableNames()[$tableName];    
        } else {
            throw new \Exception('Table ' . $tableName . ' not found');
        }
    }
    
    private static function getTableNames() 
    {
        if (self::$_tableNames === null) {
            $dependencies = self::getOuterDependenciesStatic();
            
            self::$_tableNames = [
                'carsMarks' => $dependencies->getCarsMarksTableName(),
                'carsModels' => $dependencies->getCarsModelsTableName(),
                'carsModifications' => $dependencies->getCarsModificationsTableName(),
                'carsModificationsEngines' => $dependencies->getCarsModificationsEnginesTableName(),
                'profiles' => $dependencies->getProfielsTableName(),
                'users' => $dependencies->getUsersTableName(),
                'goods' => $dependencies->getGoodsTableName(),
                'detailsArticles' => $dependencies->getDetailsArticlesTableName(),
                'deliveryPartners' => $dependencies->getDeliveryPartnersTableName(),
                'pickingRequestPositionUsers' => $dependencies-> getPickingRequestPositionUsersTableName(),
            ];
        }
        
        return self::$_tableNames;
    }
    
    public static function getFieldNames($tableName) 
    {
        if (self::$_fieldNames === null) {
            $dependencies = self::getOuterDependenciesStatic();
            
            self::$_fieldNames = [
                'carsMarks' => [
                    'id' => $dependencies->getCarsMarksIdFieldName(),
                    'name' => $dependencies->getCarsMarksNameFieldName(),
                ],
                'carsModels' => [
                    'id' => $dependencies->getCarsModelsIdFieldName(),
                    'name' => $dependencies->getCarsModelsNameFieldName(),
                    'cars_mark_id' => $dependencies->getCarsModelsCarsMarkIdFieldName(),
                ],
                'carsModifications' => [
                    'id' => $dependencies->getCarsModificationsIdFieldName(),
                    'name' => $dependencies->getCarsModificationsNameFieldName(),
                    'cars_model_id' => $dependencies->getCarsModificationsCarsModelIdFieldName(),
                ],
                'carsModificationsEngines' => [
                    'id' => $dependencies->getCarsModificationsEnginesIdFieldName(),
                    'engine_code' => $dependencies->getCarsModificationsEnginesEngineCodeFieldName(),
                    'cars_modification_id' => $dependencies->getCarsModificationsEnginesCarsModificationIdFieldName(),
                ],
                'profiles' => [
                    'id' => $dependencies->getProfielsIdFieldName(),
                    'name' => $dependencies->getProfielsNameFieldName(),
                    'surname' => $dependencies->getProfielsSurnameFieldName(),
                    'patronymic' => $dependencies->getProfielsPatronymicFieldName(),
                    'user_id' => $dependencies->getProfilesUserIdFieldName(),
                ],
                'users' => [
                    'id' => $dependencies->getUsersIdFieldName(),
                    'login' => $dependencies->getUsersLoginFieldName(),
                    'email' => $dependencies->getUsersEmailFieldName(),
                ],
                'goods' => [
                    'id' => $dependencies->getGoodsIdFieldName(),
                    'name' => $dependencies->getGoodsNameFieldName(),
                    'price' => $dependencies->getGoodsPriceFieldName(),
                    'count' => $dependencies->getGoodsCountFieldName(),
                    'delivery_partner_id' => $dependencies->getGoodsDeliveryPartnerIdFieldName(),
                ], 
                'detailsArticles' => [
                    'id' => $dependencies->getDetailsArticlesIdFieldName(),
                    'name' => $dependencies->getDetailsArticlesNameFieldName(),
                ],
                'deliveryPartners' => [
                    'id' => $dependencies->getDeliveryPartnersIdFieldName(),
                    'name' => $dependencies->getDeliveryPartnersNameFieldName(),
                ],
                'pickingRequestPositionUsers' => [
                    'request_position_id' => $dependencies->getPickingRequestPositionUsersRequestPositionIdFieldName(),
                    'user_id' => $dependencies->getPickingRequestPositionUsersUserIdFieldName(),
                ],
            ];
        }
        
        if (isset(self::$_fieldNames[$tableName])) {
             return self::$_fieldNames[$tableName];
        } else {
            throw new \Exception('table ' . $tableName . ' not found');
        }
    }
    
    public static function getFieldName($tableName, $fieldName)
    {
        if (isset(self::getFieldNames($tableName)[$fieldName])) {
            return self::getFieldNames($tableName)[$fieldName];    
        } else {
            throw new \Exception('field ' . $fieldName . ' for table ' . $tableName . ' not found');
        }
    }
    
    private function __construct() 
    {

    }
}