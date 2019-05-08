<?php

namespace detalika\requests\components;

use yii\helpers\Url;
use yii\helpers\Html;

use execut\actions\action\adapter\viewRenderer\DynaGrid;

class DynaGridViewRenderer extends DynaGrid
{
    public function renderAddButton() {
        if ($this->isAllowedAdding) {
            //$lcfirstTitle = $this->title;
            return Html::a('Добавить', Url::to(array_merge([
                    '/' . $this->getUniqueId() . '/update',
                ], $this->urlAttributes)), [
                    'type' => 'button',
                    'data-pjax' => 0,
                    //'title' => \yii::t('execut.actions', 'Add') . ' ' . $lcfirstTitle,
                    'title' => \yii::t('execut.actions', 'Add'),
                    'class' => 'btn btn-success'
                ]) . ' ';
        }
    }
}