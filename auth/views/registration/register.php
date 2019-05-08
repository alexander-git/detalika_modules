<?php

use yii\web\View;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;
use yii\captcha\Captcha;

use detalika\auth\assets\ClientTypeProcessingScriptAsset;
use detalika\auth\assets\FirstLetterUppercaseFieldAsset;

/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var detalika\auth\models\RegistrationForm $model
 */

$this->title = 'Регистрация';
$this->params['breadcrumbs'][] = $this->title;


ClientTypeProcessingScriptAsset::register($this);
FirstLetterUppercaseFieldAsset::register($this);

$jsCode = <<<JS
    $('[data-select="name"]').firstLetterUppercaseField();
    $('[data-select="surname"]').firstLetterUppercaseField();
        
    $(document).clientTypeProcessingScript({
        clientTypeSelector : '[data-select="clientType"]',
        carServiceNameContainerSelector : '[data-select="carServiceNameContainer"]',
        carServiceClientTypeId : $carServiceClientTypeId,
        carParkClientTypeId : $carParkClientTypeId     
    });
JS;

$this->registerJs($jsCode, View::POS_READY);

?>
<div class="row">
    <div class="col-md-8 col-md-offset-2">
<?php
echo Html::errorSummary($model);
    $buttons = Html::submitButton('Отправить', ['class' => 'btn btn-primary', 'name' => 'next', 'value' => 'next']) . '&nbsp;&nbsp;'
        . Html::submitButton('Отменить', ['class' => 'btn btn-default', 'name' => 'cancel', 'value' => 'pause']);
    echo \kartik\detail\DetailView::widget([
        'formOptions' => [
            'enableAjaxValidation' => false,
        ],
        'mainTemplate' => '{detail}{buttons}',
        'mode' => \kartik\detail\DetailView::MODE_EDIT,
        'attributes' => array_merge([
        ], $model->getFormFields()),
        'model' => $model,
        'buttons2' => $buttons,
        'buttonContainer' => [],
    ]);

    /*
    <!-- Поменяли на recaptcha -->
    <?= $form->field($model, 'captcha', [
        'enableClientValidation' => false,
    ])->widget(Captcha::className(), [
        'captchaAction' => ['/auth/registration/captcha'],
    ]) ?>
    */
//                if (YII_ENV !== 'dev') {
//                    ?>
<!--                    --><?//= $form->field($model, 'recaptcha')->widget(
//                        \himiklab\yii2\recaptcha\ReCaptcha::className()
//                    ) ?>
<!--                    --><?php
//                }
    ?>

        <p class="text-center">
            <?= Html::a(Yii::t('user', 'Already registered? Sign in!'), ['/user/security/login']) ?>
        </p>
    </div>
</div>