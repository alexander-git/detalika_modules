<?php

namespace detalika\auth\controllers;

use execut\navigation\behaviors\Navigation;
use execut\navigation\behaviors\navigation\Page;
use Yii;
use yii\helpers\ArrayHelper;
use yii\captcha\CaptchaAction;

use detalika\auth\models\ClientType;
use detalika\auth\models\City;
use detalika\auth\models\RegistrationForm;

use dektrium\user\controllers\RegistrationController as BaseRegistrationController;


class RegistrationController extends BaseRegistrationController
{
    public function behaviors()
    {
        return [
            'navigation' => [
                'class' => Navigation::class,
            ],
        ];
    }

    public function actions()
    {
        $actions = parent::actions();
        
        /*
        // Поменяли на recaptcha.
        $actions['captcha'] = [
            'class' => CaptchaAction::className(),
        ]; 
        */
        
        return $actions;
    }
    
    public function actionRegister()
    {
        $this->addPage([
            'class' => Page::class,
            'name' => 'Регистрация',
            'title' => 'Регистрация',
        ]);
        $model = new RegistrationForm();
        $event = $this->getFormEvent($model);

        $this->trigger(self::EVENT_BEFORE_REGISTER, $event);
        if ($model->load(Yii::$app->request->post())) {
            $user = $model->register();
            if ($user !== null) {
                $this->trigger(self::EVENT_AFTER_REGISTER, $event);
                // Сразу же залогиним пользователя.
                Yii::$app->getUser()->login($user);

                return $this->redirect(['/clients2/active-user-profile']);
            }
        }

//        // Если форма ещё не заполнялась, определим город по IP.
//        if (empty($model->city)) {
//            $model->city = $this->getCityByIp();
//        }

        return $this->render('register', [
            'model'  => $model,
            'authClientTypeIdsList' => $this->getAuthClientTypeIdsList(),
            'citiesList' => City::getAllCitiesList(),
            'carServiceClientTypeId' => ClientType::getCarServiceClientTypeId(),
            'carParkClientTypeId' => ClientType::getCarParkClientTypeId(),
        ]);
    }
    
    private function getAuthClientTypeIdsList()
    {
        $models = ClientType::find()->select(['id', 'name'])->all();
        return ArrayHelper::map($models, 'id', 'name');
    }
}
