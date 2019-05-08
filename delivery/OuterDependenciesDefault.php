<?php

namespace detalika\delivery;

class OuterDependenciesDefault implements OuterDependenciesInterface
{
    public function getModuleId()
    {
        return 'delivery';
    }
    
    public function getStocksTableName()
    {
        return 'delivery_stocks';
    }
}
