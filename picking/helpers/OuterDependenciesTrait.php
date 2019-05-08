<?php

namespace detalika\picking\helpers;


trait OuterDependenciesTrait 
{
    /**
     * @return \detalika\picking\OuterDependencesInterface
     */
    public static function getOuterDependenciesStatic() 
    {
        return OuterDependenciesHelper::getOuterDependencies();
    }
}