<?php

namespace detalika\requests\helpers;

class ModuleHelper 
{
    use OuterDependenciesTrait;
    
    private static $_module = null;
    
    /**
     * @return \detalika\requests\Module
     */
    public static function getModule()
    {
        if (self::$_module === null) {
            //self::$_module = \detalika\requests\Module::getInstance();
            
            $dependences = self::getOuterDependenciesStatic();
            $moduleId = $dependences->getModuleId();
            self::$_module = \Yii::$app->getModule($moduleId);
        }
        return self::$_module;
    }
}