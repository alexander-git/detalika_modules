<?php

namespace detalika\delivery\helpers;

use detalika\delivery\OuterDependenciesInterface;

class OuterDependenciesHelper 
{
    private static $_outerDependencies = null;
    
    /**
     * @return \detalika\delivery\OuterDependencesInterface
     */
    public static function getOuterDependencies() 
    {
        if (self::$_outerDependencies === null) {
            self::$_outerDependencies = \Yii::$container->get(OuterDependenciesInterface::class);
        }
        
        return self::$_outerDependencies;
    }
}