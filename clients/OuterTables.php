<?php

namespace detalika\clients;

use detalika\clients\helpers\OuterDependenciesTrait;

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
            throw new \Exception();
        }
    }
    
    public static function getFieldNames($tableName) 
    {
        if (self::$_fieldNames === null) {
            /**
             * @var OuterDependenciesInterface $dependencies
             */
            $dependencies = self::getOuterDependenciesStatic();
            
            self::$_fieldNames = [
                /**
                 * @todo Deprecated
                 */
                'sources' => [
                    'id' => $dependencies->getSourcesIdFieldName(),
                    'name' => $dependencies->getSourcesNameFieldName(),
                ],

                'shops' => [
                    'id' => $dependencies->getShopsIdFieldName(),
                    'name' => $dependencies->getShopsNameFieldName(),
                ],
                'users' => [
                    'id' => $dependencies->getUsersIdFieldName(),
                    'email' => $dependencies->getUsersEmailFieldName(),
                ],
                'pickingProfileUsers' => [
                    'profile_id' => $dependencies->getPickingProfileUsersProfileIdFieldName(),
                    'user_id' => $dependencies->getPickingProfileUsersUserIdFieldName(),
                ],
                'orders' => [
                    'id' => $dependencies->getOrdersIdFieldName(),
                    'clients_profile_id' => $dependencies->getOrdersProfileIdFieldName(),
                ],
                'requests' => [
                    'id' => $dependencies->getRequestsIdFieldName(),
                    'clients_profile_id' => $dependencies->getRequestsProfileIdFieldName(),
                ],
                'requestsClientCarProfiles' => [
                    'id' => $dependencies->getRequestsClientCarProfilesIdFieldName(),
                    'clients_profile_id' => $dependencies->getRequestsClientCarProfilesProfileIdFieldName(),
                ],
            ];
        }
        
        if (isset(self::$_fieldNames[$tableName])) {
             return self::$_fieldNames[$tableName];
        } else {
            throw new \Exception();
        }
    }
    
    public static function getFieldName($tableName, $fieldName)
    {
        if (isset(self::getFieldNames($tableName)[$fieldName])) {
            return self::getFieldNames($tableName)[$fieldName];    
        } else {
            throw new \Exception();
        }
    }
    
    private static function getTableNames() 
    {
        if (self::$_tableNames === null) {
            $dependencies = self::getOuterDependenciesStatic();
            
            self::$_tableNames = [
                'sources' => $dependencies->getSourcesTableName(),
                'shops' => $dependencies->getShopsTableName(),
                'users' => $dependencies->getUsersTableName(),
                'pickingProfileUsers' => $dependencies->getPickingProfileUsersTableName(),
                'orders' => $dependencies->getOrdersTableName(),
                'requests' => $dependencies->getRequestsTableName(),
            ];
        }
        
        return self::$_tableNames;
    }
   
    private function __construct() 
    {

    }
}