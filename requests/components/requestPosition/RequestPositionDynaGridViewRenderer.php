<?php

namespace detalika\requests\components\requestPosition;

use detalika\requests\components\DynaGridViewRenderer;

class RequestPositionDynaGridViewRenderer extends DynaGridViewRenderer
{
    public function getDefaultWidgetOptions()
    {
       $widgetOptions = parent::getDefaultWidgetOptions();
       $widgetOptions['class'] = RequestPositionDynaGrid::className();
       return $widgetOptions;
    }
}
