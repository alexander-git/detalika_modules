<?php

namespace detalika\clients\widgets;

use detalika\clients\OuterDependenciesInterface;
use Yii;
use yii\helpers\Url;

use execut\yii\jui\Widget;
use kartik\detail\DetailView;

use detalika\clients\models\User;
use detalika\clients\models\Profile;

class ActiveUserProfileEdit extends Widget
{
    // Используется для формирования url действия.
    public $clientsModuleId = 'clients';
    
    public function run()
    {
        if (Yii::$app->user->isGuest) {
            throw new \RuntimeException();
        }

        /**
         * @var OuterDependenciesInterface $dependencies
         */
        $dependencies = \yii::$container->get(OuterDependenciesInterface::class);
        if (!($profileModel = $dependencies->getCurrentProfile())) {
            $profileModel = new Profile();
        }

        $formName = $profileModel->formName();
        
        $profileModel->scenario = Profile::SCENARIO_EDIT_BY_ACTIVE_USER;
        $formFields = $profileModel->getFormFields();
        if ($formName !== '') {
            $get = Yii::$app->request->queryParams;
            if (isset($get[$formName])) {
                if ($profileModel->load($get)) {
                    // Какой-то странный баг. Если поля получть после валидации 
                    // возникает ошибка.
                    $formFields = $profileModel->getFormFields();                  
                    $profileModel->validate();
                }
            } 
        } else {
            // Имя формы не должно быть пустым, иначе могут быть проблемы при 
            // использовании виджета на некоторых страницах. Например для
            // страницы у которой будет get-параметр id. В этом случае параметр id
            // который придёт из действия в случае отправки post-формы и 
            // неудачной валидации перекроет его.
            throw new \LogicException();
        }
        
        
        $actionUrl = Url::to(['/' . $this->clientsModuleId . '/active-user-profile/edit', 'id' => $profileModel->id]);

        return DetailView::widget([
            'panel'=>[
                'heading' => false,
                'heading' => 'Ваши данные',
                'type'=> DetailView::TYPE_PRIMARY,
            ],
//            'mainTemplate' => '{detail}{buttons}',
            'buttons1' => '{update} ',
            'model' => $profileModel,
            'mode' => $profileModel->isNewRecord ? DetailView::MODE_EDIT : DetailView::MODE_VIEW,
            'bordered' => true,
            'striped' => true,
            'condensed' => true,
            'responsive' => true,
            'hover' => true,
            'formOptions' => [
                'action' => $actionUrl,
                'enableAjaxValidation' => true,
                'options' => [
                    'enctype'=>'multipart/form-data',
                ],
            ],
            'attributes' => $formFields,
        ]);
    }
}