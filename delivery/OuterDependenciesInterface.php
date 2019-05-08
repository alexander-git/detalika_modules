<?php

namespace detalika\delivery;

interface OuterDependenciesInterface 
{
    // Должна возвращать идентифиекатор модуля который задан ему 
    // в разделе modules приложения.
    public function getModuleId();
    
    public function getStocksTableName();
}