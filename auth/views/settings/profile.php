<?php

use yii\web\View;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;


use detalika\auth\assets\ClientTypeProcessingScriptAsset;
use detalika\auth\assets\FirstLetterUppercaseFieldAsset;

/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var detalika\auth\models\Profile $profileModel
 * @var detalika\auth\models\User $userModel
 */


$this->title = 'Настройки';
$this->params['breadcrumbs'] []= [
    'label' => 'Личный кабинет', 
    'url' => ['/user/profile/show', 'id' => Yii::$app->user->identity->id]
]; 
$this->params['breadcrumbs'][] = $this->title;


ClientTypeProcessingScriptAsset::register($this);
FirstLetterUppercaseFieldAsset::register($this);

$jsCode = <<<JS
    $(document).clientTypeProcessingScript({
        clientTypeSelector : '[data-select="clientType"]',
        carServiceNameContainerSelector : '[data-select="carServiceNameContainer"]',
        carServiceClientTypeId : $carServiceClientTypeId,
        carParkClientTypeId : $carParkClientTypeId     
    });
        
    $('[data-select="name"]').firstLetterUppercaseField();
    $('[data-select="surname"]').firstLetterUppercaseField();
JS;

$this->registerJs($jsCode, View::POS_READY);

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
                <?= Html::encode($this->title) ?>
            </div>
            <div class="panel-body">
                <?php $form = ActiveForm::begin([
                    'id' => 'profile-form',
                    'options' => ['class' => 'form-horizontal'],
                    'fieldConfig' => [
                        'template' => "{label}\n<div class=\"col-lg-9\">{input}</div>\n<div class=\"col-sm-offset-3 col-lg-9\">{error}\n{hint}</div>",
                        'labelOptions' => ['class' => 'col-lg-3 control-label'],
                    ],
                    'enableAjaxValidation'   => true,
                    'enableClientValidation' => true,
                ]); ?>

                <?= $form->field($profileModel, 'name')->textInput(['data-select' => 'name']) ?>

                <?= $form->field($profileModel, 'surname')->textInput(['data-select' => 'surname']) ?> 

                <?= $form->field($profileModel, 'clients_type_id')->dropDownList($authClientTypeIdsList, [
                    'data-select' => 'clientType',
                ]) ?>

                <div data-select="carServiceNameContainer" style="display : none">
                    <?= $form->field($profileModel, 'company_name') ?>
                </div>

                <?= $form->field($userModel, 'email') ?>

                <?= $form->field($profileModel, 'phone')->widget(MaskedInput::className(), [
                    'mask' => '+9(999)999-99-99',
                ]) ?>

                <?= $form->field($profileModel, 'city')->dropDownList($citiesList) ?>

                <?= $form->field($profileModel, 'delivery_address') ?>

                <div class="form-group">
                    <div class="col-lg-offset-3 col-lg-9">
                        <?= Html::submitButton(
                            Yii::t('user', 'Save'),
                            ['class' => 'btn btn-block btn-success']
                        ) ?>
                    </div>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

