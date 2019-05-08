<?php

namespace detalika\requests\helpers;

use detalika\requests\OuterDependenciesInterface;

class OuterDependenciesHelper 
{
    private static $_outerDependencies = null;
    
    /**
     * @return \detalika\requests\OuterDependencesInterface
     */
    public static function getOuterDependencies() 
    {
        if (self::$_outerDependencies === null) {
            self::$_outerDependencies = \Yii::$container->get(OuterDependenciesInterface::class);
        }
        
        return self::$_outerDependencies;
    }
}