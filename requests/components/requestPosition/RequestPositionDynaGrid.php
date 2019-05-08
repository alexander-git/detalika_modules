<?php

namespace detalika\requests\components\requestPosition;

use yii\helpers\Html;

use kartik\dynagrid\DynaGrid;

// Для корректной работы пришлось скопировать папку views из 
// kartik\dynagrid. Скорее всего где-то внутри кода DynaGrid пути к файлам вида 
// задаются как относительные, из-за чего при их отсутсвии рядом с виджетом
// унаследованным от DynaGrid возникают ошибки. Это ошибка в коде kartik.
class RequestPositionDynaGrid extends DynaGrid
{
    public function run()
    {
        // Так как DynaGrid не является как ни странно наследником GirdView, то
        // приходиться переопрделять функцию run().
        echo Html::tag('div', RequestPositionGridView::widget($this->gridOptions), $this->options);
    }
}