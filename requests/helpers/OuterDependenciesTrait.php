<?php

namespace detalika\requests\helpers;


trait OuterDependenciesTrait 
{
    /**
     * @return \detalika\requests\OuterDependencesInterface
     */
    public static function getOuterDependenciesStatic() 
    {
        return OuterDependenciesHelper::getOuterDependencies();
    }
}