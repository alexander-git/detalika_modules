<?php

namespace detalika\requests\components;

class UserDynaGridViewRenderer extends DynaGridViewRenderer
{
    
    public function getDefaultWidgetOptions()
    {   
        $widgetOptions = parent::getDefaultWidgetOptions();
        
        // Уберём из верхней панели таблицы все лишние кнопки и меню экспорта.
        $widgetOptions['gridOptions']['toolbar'] = [
            ['content' => $this->renderMassEditButton()],
            ['content' => $this->renderVisibleButtons()],
            ['content' => $this->renderAddButton()],
        ];

        return $widgetOptions;
    }
}