<?php

namespace detalika\picking\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

use detalika\picking\common\AccessControlTools;
use detalika\picking\models\base\ProfileUser;
use detalika\picking\models\base\RequestPositionUser;

class PickingController extends Controller
{
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'access' => [
                'class' =>  AccessControl::className(),
                'only' => [
                    'client-start-picking', 
                    'client-stop-picking',
                    'request-start-picking',
                    'request-stop-picking',
                    
                ],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => [
                            'client-start-picking', 
                            'client-stop-picking',
                            'request-position-start-picking',
                            'request-position-stop-picking',
                        ],
                        'matchCallback' => function($rule, $action) {
                            return AccessControlTools::pickingMatchCallback($rule, $action);
                        },
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'client-start-picking' => ['post'],
                    'client-stop-picking' => ['post'],
                    'request-position-start-picking' => ['post'],
                    'request-position-stop-picking' => ['post'],
                ],
            ],                    
        ]);
    }
        
    public function actionProfileStartPicking($clientProfileId, $userId)
    {
        if (ProfileUser::startPickingForProfile($clientProfileId, $userId)) {
            return $this->getSuccessJsonResponse();
        } else {
            return $this->getDefaultErrorJsonResponse();
        }
    }
    
    public function actionProfileStopPicking($clientProfileId, $userId)
    {
        if (ProfileUser::stopPickingForProfile($clientProfileId, $userId)) {
            return $this->getSuccessJsonResponse();
        } else {
            return $this->getDefaultErrorJsonResponse();
        }
    }
    
    public function actionRequestPositionStartPicking($requestPositionId, $userId)
    {
        if (RequestPositionUser::startPickingForRequestPosition($requestPositionId, $userId)) {
            return $this->getSuccessJsonResponse();
        } else {
            return $this->getDefaultErrorJsonResponse();
        }
    }
    
    public function actionRequestPositionStopPicking($requestPositionId, $userId)
    {
        if (RequestPositionUser::stopPickingForRequestPosition($requestPositionId, $userId)) {
            return $this->getSuccessJsonResponse();
        } else {
            return $this->getDefaultErrorJsonResponse();
        }
    }
    
    private function getSuccessJsonResponse() 
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'success' => true,
        ];
    }
    
    private function getDefaultErrorJsonResponse() 
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'errorMessage' => 'Произошла ошибка',
        ];
    }
}
