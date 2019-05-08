<?php

namespace detalika\requests\controllers;

use Yii;
use yii\web\BadRequestHttpException;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;

use execut\actions\Action;
use execut\actions\action\adapter\GridView as GridViewAdapter;
use execut\actions\action\adapter\viewRenderer\DynaGrid as DynaGridViewRenderer;

use detalika\requests\actions\MassCreateAction;
use detalika\requests\common\AccessControlTools;
use detalika\requests\AdvancedCrudController;
use detalika\requests\models\Request;
use detalika\requests\models\forms\RequestPositionForm;
use detalika\requests\models\forms\RequestPositionMassCreateForm;
use detalika\requests\models\search\RequestPositionSearch;
use detalika\requests\models\search\RequestPositionParentSearch;
use detalika\requests\components\requestPosition\RequestPositionDynaGridViewRenderer;

class RequestPositionController extends AdvancedCrudController
{
    use AdminNavigationFactoryTrait;
    
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'access' => [
                'class' =>  AccessControl::className(),
                'only' => ['index', 'update', 'parent-search', 'mass-create'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'update', 'parent-search', 'mass-create'],
                        'matchCallback' => function($rule, $action) {
                            return AccessControlTools::adminMatchCallback($rule, $action);
                        },
                    ],
                ],
            ],
        ]);
    }
    
    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }
        
        $actionId = $action->id;
        if (
            $actionId === 'parents-search' && 
            !Yii::$app->request->isAjax
        ) {
            throw new BadRequestHttpException('Только через ajax.');
        }
        
        if ($actionId === 'update' && $this->isCreate()) {
            $requestId = \Yii::$app->request->get('requests_request_id');
            if ($requestId !== null) {
                $baseRoute = ['mass-create',  'requests_request_id' => $requestId];
                $route = $this->navigation->getRouteWithExisting($baseRoute);
                return $this->redirect($route);
            }
        }
        
        return true;
    }
        
    protected function getEditModelClassName()
    {
        return RequestPositionForm::className();
    }
    
    protected function getSearchModelClassName() 
    {
        return RequestPositionSearch::className();
    }
    
    protected function getModelsListTitle() 
    {
        return $this->pageBuilder->getRequestPositionIndexPageTitle();
    }
    
    protected function getTextAttributeNameForAjaxSearch() 
    {
        return 'positionName';
    }
    
    protected  function getIdAttributeName()
    {
        return 'id';
    }
    
    public function actions()
    {
        $moduleId = $this->module->getUniqueId();
        $navigation = $this->navigation;
        
        $baseUrlAttributes = [];
        $urlAttributes = $navigation->getRouteFromRequestPositionExisitng($baseUrlAttributes);
        $requestMessageControllerId = $this->routeItems->getRequestMessageControllerId();
        
        return ArrayHelper::merge(parent::actions(), [
            'index' => [
                'adapter' => [
                    'view' => [
                        // Для возможности менять строку в GridView при выводе позиции.
                        'class' => RequestPositionDynaGridViewRenderer::className(),
                    ],
                ],
            ],
            
            'update' => [
                'adapter' => [
                    'editAdapterConfig' => [
                        'additionalAttributes' => [
                            'requests_request_id',
                        ],
                    ],
                    'relationAdapterConfig' => [
                        'requestMessagesInRequestPosition' => [
                            'view' => [
                                'title' => 'Сообщения',
                                'uniqueId' => '/' .  $moduleId . '/' .$requestMessageControllerId,
                                'urlAttributes' => $urlAttributes,
                            ],
                        ],
                    ],
                    'relations' => [
                        'requestMessagesInRequestPosition',
                    ],
                ],
            ],
            'parents-search' => [
                'class' => Action::className(),
                'adapter' => [
                    'class' => GridViewAdapter::className(),
                    'model' => RequestPositionParentSearch::className(),
                    'view' => [
                        'class' => DynaGridViewRenderer::className(),
                        'modelClass' => RequestPositionParentSearch::className(),
                    ],       
                    'attributes' => [
                        $this->getIdAttributeName(),
                        'text' => 'positionName',
                    ],
                ],                
            ], 
            'mass-create' => [
                'class' => MassCreateAction::className(),
                'formModelClass' => RequestPositionMassCreateForm::className(),
                'redirectUrl' => function($model) use ($navigation) {
                    if (!empty($model->requests_request_id)) {
                        $baseRoute = ['index', 'requests_request_id' => $model->requests_request_id];
                        $route = $navigation->getRouteWithExisting($baseRoute);
                        return Url::to($route);
                    } else {
                        $baseRoute = ['index'];
                        $route = $navigation->getRouteWithExisting($baseRoute);
                        return Url::to($route);
                    }
                },
            ],
        ]);
    } 
    
    protected function getCreatePageTitle() 
    {
        return $this->pageBuilder->getRequestPositionCreatePageTitle();
    }
    
    protected function getUpdatePage()
    {
        $requestPosition = $this->findModel();
        return $this->getUpdatePageCommon($requestPosition);
    }

    protected function getPages()
    {
        $isCreateOrUpdate = $this->isCreate() || $this->isUpdate();

        $isMoveFromClientCarPage = $this->navigation->isMoveFromClientCarPage();
        $isMoveFromRequestPage = $this->navigation->isMoveFromRequestPage();
        
        if ($isCreateOrUpdate && $isMoveFromClientCarPage && $isMoveFromRequestPage) {
            return $this->getPagesForCreateUpdateActionWhenMoveFromClientCarPage();
        } 
        
        if ($isCreateOrUpdate && $isMoveFromRequestPage) {
            return $this->getPagesForCreateUpdateActionWhenMoveFromRequestPage();
        }
        
        if ($isCreateOrUpdate) {
            return $this->getPagesForCreateUpdateActionDefault(); 
        }
        
        $isMassCreate = $this->action->id === 'mass-create';
        if ($isMassCreate && $isMoveFromClientCarPage && $isMoveFromRequestPage) {
            return $this->getPagesForMassCreateActionWhenMoveFromClientCarPage();
        }
        
        if ($isMassCreate && $isMoveFromRequestPage) {
            return $this->getPagesForMassCreateActionWhenMoveFromRequestPage();
        }
        
        if ($isMassCreate) {
            return $this->getPagesForMassCreateActionDefault();
        }
        
        return parent::getPages();
    }
    
    protected function getMassCreatePage()
    {
        return $this->pageBuilder->getRequestPositionMassCreatePage();
    }
        
    private function getPagesForCreateUpdateActionDefault() 
    {
        $pages = [];
        $pages []= $this->getMainPage();
        $pages []= $this->getIndexPage();
        
        if ($this->isUpdate()) {
            $requestPosition = $this->findModel();
            $request = $requestPosition->request;
            $pages []= $this->pageBuilder->getRequestUpdatePage($request);
            $pages []= $this->getUpdatePageCommon($requestPosition);
        } 
        
        if ($this->isCreate()) {
            $pages []= $this->getCreatePage();
        }
        
        return $pages;
    }
    
    private function getPagesForCreateUpdateActionWhenMoveFromClientCarPage()
    {
        $pages = [];
        $pages []= $this->getMainPage();
        $pages []= $this->pageBuilder->getClientCarIndexPage();
        
        if ($this->isUpdate()) {        
            $requestPosition = $this->findModel();
            $request = $requestPosition->request;
            $clientCar = $request->clientCar;
            $pages []=  $this->pageBuilder->getClientCarUpdatePage($clientCar);
            $pages []= $this->pageBuilder->getRequestUpdatePage($request);
            $pages []= $this->getUpdatePageCommon($requestPosition);
        }   
        
        if ($this->isCreate()) {
            $request = $this->findRequestOnGetParam();
            if ($request !== null) {
                $clientCar = $request->clientCar;
                $pages []=  $this->pageBuilder->getClientCarUpdatePage($clientCar);
                $pages []= $this->pageBuilder->getRequestUpdatePage($request);
            }
            
            $pages []= $this->getCreatePage();
        }
        
        return $pages;
    }
    
    private function getPagesForCreateUpdateActionWhenMoveFromRequestPage()
    {
        $pages = [];
        $pages []= $this->getMainPage();
        $pages []= $this->pageBuilder->getRequestIndexPage();
        
        if ($this->isUpdate()) {        
            $requestPosition = $this->findModel();
            $request = $requestPosition->request;
            $pages [] = $this->pageBuilder->getRequestUpdatePage($request);
            $pages [] = $this->getUpdatePageCommon($requestPosition);
        }             
        
        if ($this->isCreate()) {
            $request = $this->findRequestOnGetParam();
            if ($request !== null) {
                $pages []= $this->pageBuilder->getRequestUpdatePage($request);
            }
            $pages []= $this->getCreatePage();            
        }

        return $pages;
    }
    
    private function getPagesForMassCreateActionDefault() 
    {
        $pages = [];
        $pages []= $this->getMainPage();
        $pages []= $this->getIndexPage();
        $pages []= $this->getMassCreatePage();
     
        return $pages;
    }
    
    private function getPagesForMassCreateActionWhenMoveFromClientCarPage()
    {
        $pages = [];
        $pages []= $this->getMainPage();
        $pages []= $this->pageBuilder->getClientCarIndexPage();
        
        $request = $this->findRequestOnGetParam();
        if ($request !== null) {
            $clientCar = $request->clientCar;
            $pages []=  $this->pageBuilder->getClientCarUpdatePage($clientCar);
            $pages []= $this->pageBuilder->getRequestUpdatePage($request);
        }
            
        $pages []= $this->getMassCreatePage();
    }
    
    private function getPagesForMassCreateActionWhenMoveFromRequestPage()
    {
        $pages = [];
        $pages []= $this->getMainPage();
        $pages []= $this->pageBuilder->getRequestIndexPage();
            
        $request = $this->findRequestOnGetParam();
        if ($request !== null) {
            $pages []= $this->pageBuilder->getRequestUpdatePage($request);
        }
        $pages []= $this->getMassCreatePage();            

        return $pages;
    }
    
    private function getUpdatePageCommon($requestPosition) 
    {
        return $this->pageBuilder->getRequestPositionUpdatePage($requestPosition);
    }
    
    private function findRequestOnGetParam()
    {
        $requestId = \Yii::$app->request->get('requests_request_id');
        if ($requestId === null) {
            return null;
        }
        
        return Request::findOne(['id' => $requestId]);
    }
}