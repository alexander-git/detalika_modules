<?php

use kartik\detail\DetailView;

?>
<?= DetailView::widget([
    'panel'=>[
        'heading'=>'Редактирование',
        'type'=> DetailView::TYPE_PRIMARY,
    ],
    'buttons1' => '{update}',
    'model' => $model,
    'mode' => 'edit',
    'bordered' => true,
    'striped' => true,
    'condensed' => true,
    'responsive' => true,
    'hideIfEmpty' => true,
    'hover' => true,
    'formOptions' => [
        'options' => [
            'enctype'=>'multipart/form-data',
        ],
    ],
    'attributes' => $model->getFormFields(),
]) ?>