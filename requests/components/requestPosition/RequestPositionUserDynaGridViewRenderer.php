<?php

namespace detalika\requests\components\requestPosition;

use detalika\requests\components\UserDynaGridViewRenderer;

class RequestPositionUserDynaGridViewRenderer extends UserDynaGridViewRenderer
{
    public function getDefaultWidgetOptions()
    {
        $widgetOptions = parent::getDefaultWidgetOptions();
        $widgetOptions['class'] = RequestPositionDynaGrid::className();
        return $widgetOptions;
    }
}
