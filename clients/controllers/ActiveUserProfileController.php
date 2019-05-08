<?php

namespace detalika\clients\controllers;

use detalika\clients\models\User;
use detalika\clients\OuterDependenciesInterface;
use execut\navigation\behaviors\navigation\Page;
use execut\navigation\behaviors\Navigation;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\web\Response;
use yii\widgets\ActiveForm;
use yii\web\ForbiddenHttpException;

use detalika\clients\CrudController;
use detalika\clients\models\Profile;

class ActiveUserProfileController extends CrudController
{
    public function behaviors()
    {
        $mainPageTitle = 'Главная';
        $indexPageTitle = 'Личный кабинет';
        $pages = [
            [
                'class' => Page::className(),
                'params' => [
                    'url' => [
                        '/'
                    ],
                    'name' => $mainPageTitle,
                    'header' => $mainPageTitle,
                    'title' => $mainPageTitle,
                ],
            ],
            [
                'class' => Page::className(),
                'params' => [
                    'url' => [
                        '/' . $this->getUniqueId() . '/index',
                    ],
                    'name' => $indexPageTitle,
                    'header' => $indexPageTitle,
                    'title' => $indexPageTitle,
                ],
            ],
        ];

        return array_merge([
            'navigation' => [
                'class' => Navigation::className(),
                'pages' => $pages,
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow'         => true,
                        'roles'         => ['@'],
                    ]
                ],
            ],
        ], parent::behaviors());
    }

    public function actionIndex() {
        return $this->render('index');
    }

    public function actionEdit()
    {

        /**
         * @var OuterDependenciesInterface $dependencies
         */
        $dependencies = \yii::$container->get(OuterDependenciesInterface::class);
        if (!($model = $dependencies->getCurrentProfile())) {
            $model = new Profile();
            $userId = \Yii::$app->user->id;
            $model->user_id = $userId;
        }

        //$model->scenario = Profile::SCENARIO_EDIT_BY_ACTIVE_USER;
        
        $request = Yii::$app->request;
        if (($request->isAjax || $request->isPjax) &&  $model->load($request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $result = ActiveForm::validate($model);            
            return $result;
        }
        
        $post = $request->post();
        if (
            $model->load($post) &&  
            $model->save()
        ) {
            Yii::$app->session->setFlash('kv-detail-success', 'Профиль успешно обновлён');
            return $this->redirect($this->getRedirectRouteWithFormDataInGetParams($model));
        }

        Yii::$app->session->setFlash('error', Html::errorSummary($model));
        return $this->redirect($this->getRedirectRouteWithFormDataInGetParams($model));
    }    
    
    private function getRedirectRouteWithFormDataInGetParams($profileModel)
    {
        $redirectRoute = Yii::$app->request->referrer;
        $post = Yii::$app->request->post();
        $formName = $profileModel->formName();
        if ($formName === '') {
            // Имя формы не должно быть пустым. Так как нам нужно удалить 
            // предыдущие значение атрибутов модели из строки запроса, чтобы 
            // при перенаправление обратно с новыми значениями в get они 
            // не добовлялись(возможно и с разными значениями) по нескольку раз.
            // Имя формы поможет найти старые значения атрибутов модели
            // в строке запроса.
            throw new \LogicException();
        }

        if (strpos($redirectRoute, $formName) !== false) {
            // Очиситим предыдущие строку запроса от предыдущих значений
            // аттрибутов модели, если они были.
            $redirectRoute = substr($redirectRoute, 0, strpos($redirectRoute, $formName));
            
            if ($redirectRoute[strlen($redirectRoute) - 1] === '?') {
                // Если в конце строки остался только '?' обрежем его.
                $redirectRoute = substr($redirectRoute, 0, strlen($redirectRoute) - 1);
            }
        }
        
        
        unset($post['_csrf-backend']); 
        // Хитрый способ формирования Url. Сначала сформируем дополнительные 
        // get-параметры со значениями модели профиля, которые были отправлены 
        // через POST. Потомо допишем их к referer. 
        $relativeUrl = Url::to(array_merge(['/'], $post));
        $additionalGetParamsStr = substr($relativeUrl, strpos($relativeUrl, '?') + 1);
        if (strpos($redirectRoute, '?') === false) {
          $redirectRoute .= '?';
        } else {
           $redirectRoute .= '&'; 
        }
        $redirectRoute .= $additionalGetParamsStr;
        Yii::error($redirectRoute);
        return $redirectRoute;
    }
}