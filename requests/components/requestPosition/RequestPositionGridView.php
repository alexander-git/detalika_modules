<?php

namespace detalika\requests\components\requestPosition;

use detalika\requests\helpers\OuterDependenciesTrait;

use kartik\grid\GridView;

class RequestPositionGridView extends GridView
{
    use OuterDependenciesTrait;
    
    public function renderTableRow($model, $key, $index) 
    {
        $standartRowOutput = parent::renderTableRow($model, $key, $index);
        $dependencies = self::getOuterDependenciesStatic();
        return $dependencies->renderRequestPositionTableRow($model, $key, $index, $standartRowOutput, $this);
    }
}