<?php

namespace detalika\delivery\helpers;


trait OuterDependenciesTrait 
{
    /**
     * @return \detalika\delivery\OuterDependencesInterface
     */
    public static function getOuterDependenciesStatic() 
    {
        return OuterDependenciesHelper::getOuterDependencies();
    }
}