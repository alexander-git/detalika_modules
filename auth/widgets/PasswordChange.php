<?php
/**
 * Created by PhpStorm.
 * User: execut
 * Date: 3/29/17
 * Time: 10:44 AM
 */

namespace detalika\auth\widgets;


use detalika\auth\models\PasswordChangeForm;
use Yii;
use execut\yii\jui\Widget;
use kartik\detail\DetailView;
use yii\helpers\Url;

class PasswordChange extends Widget
{
    public function run() {
        $user = Yii::$app->user->identity;
        $model = new PasswordChangeForm($user);
        if ($model->load(Yii::$app->request->post()) && $model->changePassword()) {
            Yii::$app->session->setFlash('success', 'Ваш пароль успешно изменён');
            return \yii::$app->controller->refresh();
        }

        $model->current_password = null;
        $model->new_password = null;
        $model->new_password_repeat = null;

        $container = '';
//        $container .= $this->_beginContainer();
        $container .= DetailView::widget([
            'panel'=>[
                'heading' => false,
                'heading' => 'Смена пароля',
                'type'=> DetailView::TYPE_PRIMARY,
            ],
//            'mainTemplate' => '{detail}{buttons}',
            'buttons1' => '{update} ',
            'model' => $model,
//            'mode' => DetailView::MODE_EDIT,
            'bordered' => true,
            'striped' => true,
            'condensed' => true,
            'responsive' => true,
            'hover' => true,
            'formOptions' => [
//                'id' => 'changePassword',
                'action' => Url::to(['/auth/settings/password-change']),
                'enableAjaxValidation' => true,
                'options' => [
//                    'enctype'=>'multipart/form-data',
                ],
            ],
            'attributes' => [
                'current_password' => [
                    'attribute' => 'current_password',
                    'type' => DetailView::INPUT_PASSWORD,
                ],
                'new_password' => [
                    'attribute' => 'new_password',
                    'type' => DetailView::INPUT_PASSWORD,
                ],
                'new_password_repeat' => [
                    'attribute' => 'new_password_repeat',
                    'type' => DetailView::INPUT_PASSWORD,
                ],
            ],
        ]);
//        $container .= $this->_endContainer();

        return $container;
    }
}