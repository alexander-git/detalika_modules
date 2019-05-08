<?php

namespace detalika\auth\controllers;

use Yii;
use yii\helpers\ArrayHelper;

use detalika\auth\models\User;
use detalika\auth\models\Profile;
use detalika\auth\models\City;
use detalika\auth\models\ClientType;
use detalika\auth\models\PasswordChangeForm;

use dektrium\user\controllers\SettingsController as BaseSettingsController;
use yii\web\Response;
use yii\widgets\ActiveForm;

class SettingsController extends BaseSettingsController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['access']['rules'] []= [
            'allow'   => true,
            'actions' => ['password-change'],
            'roles'   => ['@'],
        ];
        
        return $behaviors;
    }
    
    public function actionProfile()
    {
        $profileModel = $this->finder->findProfileById(Yii::$app->user->identity->getId());

        $userModel = $profileModel->user;
        
        $profileModel->setScenario(Profile::SCENARIO_UPDATE_BY_CLIENT);
        $userModel->setScenario(User::SCENARIO_UPDATE_BY_CLIENT);
        
        $event = $this->getProfileEvent($profileModel);
        
        $this->performAjaxValidation($profileModel);
        $this->performAjaxValidation($userModel);
        
        $this->trigger(self::EVENT_BEFORE_PROFILE_UPDATE, $event);
        
        $post = Yii::$app->request->post();
        if ($profileModel->load($post) && $userModel->load($post)) {
            if ($profileModel->updateByClient($userModel)) {
                Yii::$app->getSession()->setFlash(
                    'success', 
                    Yii::t('user', 'Your profile has been updated')
                );
                $this->trigger(self::EVENT_AFTER_PROFILE_UPDATE, $event);
                return $this->refresh();
            }
        }

        return $this->render('profile', [
            'profileModel' => $profileModel,
            'userModel' => $userModel,
            'authClientTypeIdsList' => $this->getAuthClientTypeIdsList(),
            'citiesList' => City::getAllCitiesList(),
            'carServiceClientTypeId' => ClientType::getCarServiceClientTypeId(),
            'carParkClientTypeId' => ClientType::getCarParkClientTypeId(),
        ]); 
    }
    
    public function actionPasswordChange()
    {
        $user = Yii::$app->user->identity;
        $model = new PasswordChangeForm($user);
        if ($model->load(Yii::$app->request->post())) {
            if (\yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }

            if ($model->changePassword()) {
                Yii::$app->session->setFlash('kv-detail-success', 'Ваш пароль успешно изменён');
                return $this->redirect(\yii::$app->request->referrer);
            }
        }
        
        $model->current_password = null;
        $model->new_password = null;
        $model->new_password_repeat = null;
        
        return $this->render('passwordChange', [
            'model' => $model
        ]);
    }
    
    private function getAuthClientTypeIdsList()
    {
        $models = ClientType::find()->select(['id', 'name'])->all();
        return ArrayHelper::map($models, 'id', 'name');
    }     
}
