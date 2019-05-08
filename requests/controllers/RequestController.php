<?php

namespace detalika\requests\controllers;

use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;

use detalika\requests\common\AccessControlTools;
use detalika\requests\AdvancedCrudController;
use detalika\requests\models\ClientCar;
use detalika\requests\models\forms\RequestForm;
use detalika\requests\models\search\RequestSearch;
use detalika\requests\components\requestPosition\RequestPositionDynaGridViewRenderer;

class RequestController extends AdvancedCrudController
{
    use AdminNavigationFactoryTrait;
    
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'access' => [
                'class' =>  AccessControl::className(),
                'only' => ['index', 'update'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'update'],
                        'matchCallback' => function($rule, $action) {
                            return AccessControlTools::adminMatchCallback($rule, $action);
                        },
                    ],
                ],
            ],
        ]);
    }
        
    protected function getEditModelClassName()
    {
        return RequestForm::className();
    }
    
    protected function getSearchModelClassName() 
    {
        return RequestSearch::className();
    }
    
    protected function getModelsListTitle() 
    {
        return $this->pageBuilder->getRequestIndexPageTitle();
    }
    
    protected function getTextAttributeNameForAjaxSearch() 
    {
        return 'id';
    }
    
    public function actions()
    {
        $moduleId = $this->module->getUniqueId();

        $baseUrlAttributes = [];
        $urlAttributes = $this->navigation->getRouteFromRequestExisitng($baseUrlAttributes);
        $requestPositionControllerId = $this->routeItems->getRequestPositionControllerId();
        $requestMessageControllerId = $this->routeItems->getRequestMessageControllerId();
        
        return ArrayHelper::merge(parent::actions(), [
            'update' => [
                'adapter' => [
                    'relationAdapterConfig' => [
                        'requestPositionsInRequest' => [
                            'view' => [
                                // Для возможности менять строку в GridView при выводе позиции.
                                'class' => RequestPositionDynaGridViewRenderer::className(),
                                'title' => 'Позиции',
                                'uniqueId' => '/' .$moduleId . '/' .$requestPositionControllerId,
                                'urlAttributes' => $urlAttributes,
                                
                                // Уберём все фильтры и ненужные кнопки и заголовки всех столбцов.           
                                'widget' => [
                                    'gridOptions' => [
                                        'filterModel' => null,
                                        'showHeader' => null,
                                    ],
                                ],
                            ],
                        ], 
                        'requestMessagesInRequest' => [
                            'view' => [
                                'title' => 'Сообщения',
                                'uniqueId' => '/' .$moduleId . '/' .$requestMessageControllerId,
                                'urlAttributes' => $urlAttributes,
                            ],
                        ],
                    ],
                    'relations' => [
                        'requestPositionsInRequest',
                        'requestMessagesInRequest',
                    ],
                    // Для установки отображения запрсов относящихся к определённому 
                    // автомобилю.
                    // Пока закомментируем так как при создании нам это не нужно.
                    /*
                    'editAdapterConfig' => [
                        'additionalAttributes' => [
                            'requests_client_car_id',
                        ],
                    ], 
                    */
                ],
            ],
        ]);
    }     

    protected function getPages()
    {
        if ($this->isUpdate() && $this->navigation->isMoveFromClientCarPage()) {
            return $this->getPagesForUpdateWhenMoveFromClientCar();
        }
        
        if ($this->isIndex() && $this->navigation->isMoveFromClientCarPage()) {
            return $this->getPagesForIndexWhenMoveFromClientCar();
        }
           
        return parent::getPages();
    }
    
    protected function getCreatePageTitle() 
    {
        return $this->pageBuilder->getRequestCreatePageTitle();
    }
    
    protected function getUpdatePage()
    {
        $request = $this->findModel();
        return $this->getUpdatePageCommon($request);
    }    
    
    private function getUpdatePageCommon($request) 
    {
        return $this->pageBuilder->getRequestUpdatePage($request);
    }
    
    private function getPagesForUpdateWhenMoveFromClientCar()
    {
        $request = $this->findModel(); 
        $pages = [];
        $pages [] = $this->getMainPage();
        $pages [] = $this->getIndexPage();
        $pages [] = $this->pageBuilder->getClientCarUpdatePage($request->clientCar);
        $pages[] = $this->getUpdatePageCommon($request);
        return $pages;
    }
    
    private function getPagesForIndexWhenMoveFromClientCar()
    {
        $clientCar = $this->findClientCarOnGetParam();
        
        $pages = [];
        
        $pages [] = $this->getMainPage();
        $pages [] = $this->pageBuilder->getClientCarIndexPage();
        if ($clientCar !== null) {
            $pages [] = $this->pageBuilder->getClientCarUpdatePage($clientCar );
        }
        
        if ($clientCar !== null) {
            $pages []= $this->pageBuilder->getRequestIndexPageWhenMoveFromClientCar($clientCar);
        } else {
             $pages [] = $this->getIndexPage();    
        }
        
        return $pages;
    }
    
    private function findClientCarOnGetParam()
    {
        $clientCarId = \Yii::$app->request->get('requests_client_car_id');
        if ($clientCarId === null) {
            return null;
        }
        
        $clientCar = ClientCar::findOne(['id' => $clientCarId]);
        if ($clientCar === null) {
            throw new NotFoundHttpException();
        }
        
        return $clientCar;
    }
}