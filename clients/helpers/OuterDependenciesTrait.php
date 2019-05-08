<?php

namespace detalika\clients\helpers;

use detalika\clients\OuterDependenciesInterface;

trait OuterDependenciesTrait 
{
    private static $_outerDependencies = null;
    
    /**
     * @return \detalika\requests\OuterDependencesInterface
     */
    public static function getOuterDependenciesStatic() 
    {
        if (self::$_outerDependencies === null) {
            self::$_outerDependencies = \Yii::$container->get(OuterDependenciesInterface::class);
        }
        
        return self::$_outerDependencies;
    }
}