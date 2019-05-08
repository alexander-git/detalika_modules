<?php

namespace detalika\picking\helpers;

use detalika\picking\OuterDependenciesInterface;

class OuterDependenciesHelper 
{
    private static $_outerDependencies = null;
    
    /**
     * @return \detalika\picking\OuterDependencesInterface
     */
    public static function getOuterDependencies() 
    {
        if (self::$_outerDependencies === null) {
            self::$_outerDependencies = \Yii::$container->get(OuterDependenciesInterface::class);
        }
        
        return self::$_outerDependencies;
    }
}