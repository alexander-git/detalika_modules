<?php

namespace detalika\delivery\common;

use detalika\delivery\helpers\OuterDependenciesTrait;

class InnerTables 
{
    use OuterDependenciesTrait;
    
    private static $_tableNames = null;
    
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
                'stocks' => $dependencies->getStocksTableName(),
            ];
        }
        
        return self::$_tableNames;
    }
        
    private function __construct() 
    {

    }
}