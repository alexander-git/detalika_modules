<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Смена пароля';
$this->params['breadcrumbs'] []= [
    'label' => 'Личный кабинет', 
    'url' => ['/user/profile/show', 'id' => Yii::$app->user->identity->id]
]; 
$this->params['breadcrumbs'][] = $this->title;
?>
<p>
    <?= Html::a('Общая информация', ['/user/profile/show', 'id' => Yii::$app->user->identity->id], ['class' => 'btn btn-primary']) ?>
    <?= Html::a('Настройки', ['/auth/settings/profile'], ['class' => 'btn btn-success']) ?>
</p>
<div class="row">
    <div class="col-md-9">
        <p class="bg-info" style="padding : 10px;">
            Редактирование личных данных
        </p>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?= Html::encode($this->title) ?></h3>
            </div>
            <div class="panel-body">
                <?php $form = ActiveForm::begin([
                    'id'          => 'passowrdChangeForm',
                    'options'     => ['class' => 'form-horizontal'],
                    'fieldConfig' => [
                        'template'     => "{label}\n<div class=\"col-lg-9\">{input}</div>\n<div class=\"col-sm-offset-3 col-lg-9\">{error}\n{hint}</div>",
                        'labelOptions' => ['class' => 'col-lg-3 control-label'],
                    ],
                    'enableAjaxValidation'   => false,
                    'enableClientValidation' => true,
                ]); ?>

  
                <?= $form->field($model, 'current_password')->passwordInput() ?>

                <?= $form->field($model, 'new_password')->passwordInput() ?>
                
                <?= $form->field($model, 'new_password_repeat')->passwordInput() ?>
                
                <div class="form-group">
                    <div class="col-lg-offset-3 col-lg-9">
                        <?= Html::submitButton(Yii::t('user', 'Save'), ['class' => 'btn btn-block btn-success']) ?>
                    </div>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>


