<?php

namespace detalika\requests\controllers;

use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;

use detalika\requests\common\AccessControlTools;
use detalika\requests\AdvancedCrudController;
use detalika\requests\models\Request;
use detalika\requests\models\RequestPosition;
use detalika\requests\models\forms\RequestMessageForm;
use detalika\requests\models\search\RequestMessageSearch;

class RequestMessageController extends AdvancedCrudController
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
        return RequestMessageForm::className();
    }
    
    protected function getSearchModelClassName() 
    {
        return RequestMessageSearch::className();
    }
    
    protected function getModelsListTitle() 
    {
        return $this->pageBuilder->getRequestMessageIndexPageTitle();
    }
    
    public function actions()
    {
        return ArrayHelper::merge(parent::actions(), [
            'update' => [
                'adapter' => [
                    'editAdapterConfig' => [
                        'additionalAttributes' => [
                            'requests_request_id',
                            // Для правильного создания сообщения из GridView
                            // распологающегося под формой редактирования
                            // позиции запроса.
                            'requests_request_position_id',
                        ],
                    ],
                ],
            ],
        ]);
    }     
    
    protected function getCreatePageTitle() 
    {
        return $this->pageBuilder->getRequestMessageCreatePageTitle();
    }
    
    protected function getUpdatePage()
    {
        $requestMessage = $this->findModel();
        return $this->getUpdatePageCommon($requestMessage);
    }
    
    protected function getPages()
    {
        $isCreateOrUpdate = $this->isCreate() || $this->isUpdate();
        
        $isMoveFromClientCarPage = $this->navigation->isMoveFromClientCarPage();
        $isMoveFromRequestPage = $this->navigation->isMoveFromRequestPage();
        $isMoveFromRequestPositionPage = $this->navigation->isMoveFromRequestPositionPage();
        
        if ($isCreateOrUpdate) {
            // От автомобиля до сообщения можно дойти 2 путями:
            // 1) Автомобиль -> запрос -> сообщение
            // 2) Автомобиль -> запрос -> позиция запроса -> сообщение. 
            if (
                ($isMoveFromClientCarPage && $isMoveFromRequestPage) ||
                ($isMoveFromClientCarPage && $isMoveFromRequestPage && $isMoveFromRequestPositionPage) 
            ) {
                return $this->getPagesWhenMoveFromClientCarPage();
            }
        } 
        
        if ($isCreateOrUpdate) {
            if (
                ($isMoveFromRequestPage) ||
                ($isMoveFromRequestPage && $isMoveFromRequestPositionPage)
            ) {
                return $this->getPagesWhenMoveFromRequestPage();
            }
        }
        
        if ($isCreateOrUpdate) {
            if (!$isMoveFromRequestPage && $isMoveFromRequestPositionPage) {
                return $this->getPageesWhenMoveFromRequestPositionPage();
            }
        }
        
        if ($isCreateOrUpdate) {
            return $this->getPagesForCreateUpdateActionDefault(); 
        }
        
        return parent::getPages();
    }
        
    private function getPagesForCreateUpdateActionDefault() 
    {
        $pages = [];
        $pages []= $this->getMainPage();
        $pages []= $this->getIndexPage();
        
        if ($this->isUpdate()) {
            $requestMessage = $this->findModel();
            $request = $requestMessage->request;
            $requestPosition = $requestMessage->requestPosition;
            
            $pages []= $this->pageBuilder->getRequestUpdatePage($request);
            if ($requestPosition !== null) {
                $pages []= $this->pageBuilder->getRequestPositionUpdatePage($requestPosition);
            }
            
            $pages []= $this->getUpdatePageCommon($requestMessage);
        } 
        
        if ($this->isCreate()) {
            $pages []= $this->getCreatePage();
        }
        
        return $pages;
    }
    
    private function getPagesWhenMoveFromClientCarPage()
    {
        $pages = [];
        $pages []= $this->getMainPage();
        $pages []= $this->pageBuilder->getClientCarIndexPage();
         
        if ($this->isUpdate()) {        
            $requestMessage = $this->findModel();
            $request = $requestMessage->request;
            $requestPosition = $requestMessage->requestPosition;
            $clientCar = $request->clientCar;
            
            $pages []=  $this->pageBuilder->getClientCarUpdatePage($clientCar);
            $pages []= $this->pageBuilder->getRequestUpdatePage($request);
            if ($requestPosition !== null) {
                $pages []= $this->pageBuilder->getRequestPositionUpdatePage($requestPosition);
            }
            
            $pages []= $this->getUpdatePageCommon($requestMessage);
        }   
        
        
        if ($this->isCreate()) {
            // От автомобиля до сообщения можно дойти 2 путями:
            // 1) Автомобиль -> запрос -> сообщение
            // 2) Автомобиль -> запрос -> позиция запроса -> сообщение.
            $isMoveFromRequestPage = $this->navigation->isMoveFromRequestPage();
            $isMoveFromRequestPositionPage = $this->navigation->isMoveFromRequestPositionPage();
            
            $clientCar = null;
            $request = null;
            $requestPosition = null;
            
            if ($isMoveFromRequestPage && $isMoveFromRequestPositionPage) {
               $requestPosition = $this->findRequestPositionOnGetParam();
               if ($requestPosition !== null) {
                    $request = $requestPosition->request;
                    $clientCar = $request->clientCar;
               }
            }
            
            if ($isMoveFromRequestPage && !$isMoveFromRequestPositionPage) {
                $request = $this->findRequestOnGetParam();
                if ($request !== null) {
                    $clientCar = $request->clientCar; 
                }
            }
            
            
            if ($clientCar !== null) {
                $pages []=  $this->pageBuilder->getClientCarUpdatePage($clientCar);
            }
            
            if ($request !== null) {
                $pages []= $this->pageBuilder->getRequestUpdatePage($request);
            }
            
            if ($requestPosition !== null) {
                $pages []= $this->pageBuilder->getRequestPositionUpdatePage($requestPosition);
            }
                
            $pages []= $this->getCreatePage();
        }
        
        return $pages;
    }
    
    private function getPagesWhenMoveFromRequestPage()
    {
        $pages = [];
        $pages []= $this->getMainPage();
        $pages []= $this->pageBuilder->getRequestIndexPage();
        
        if ($this->isUpdate()) {        
            $requestMessage = $this->findModel();
            $request = $requestMessage->request;
            $requestPosition = $requestMessage->requestPosition;
            $pages [] = $this->pageBuilder->getRequestUpdatePage($request);
            if ($requestPosition !== null) {
                $pages []= $this->pageBuilder->getRequestPositionUpdatePage($requestPosition);
            }
            
            $pages [] = $this->getUpdatePageCommon($requestMessage);
        }             
        
        if ($this->isCreate()) {
            $isMoveFromRequestPage = $this->navigation->isMoveFromRequestPage();
            $isMoveFromRequestPositionPage = $this->navigation->isMoveFromRequestPositionPage();
            
            $request = null;
            $requestPosition = null;
            
            if ($isMoveFromRequestPage && $isMoveFromRequestPositionPage) {
               $requestPosition = $this->findRequestPositionOnGetParam();
               if ($requestPosition !== null) {
                    $request = $requestPosition->request;
               }
            }
            
            if ($isMoveFromRequestPage && !$isMoveFromRequestPositionPage) {

                $request = $this->findRequestOnGetParam();
            }
            
            if ($request !== null) {
                $pages []= $this->pageBuilder->getRequestUpdatePage($request);
            }
            
            if ($requestPosition !== null) {
                $pages []= $this->pageBuilder->getRequestPositionUpdatePage($requestPosition);
            }
            
            $pages []= $this->getCreatePage();            
        }

        return $pages;
    }
    
    private function getPageesWhenMoveFromRequestPositionPage()
    {
        $pages = [];
        $pages []= $this->getMainPage();
        $pages []= $this->pageBuilder->getRequestIndexPage();
        
        if ($this->isUpdate()) {        
            $requestMessage = $this->findModel();
            $request = $requestMessage->request;
            $requestPosition = $requestMessage->requestPosition;
            $pages [] = $this->pageBuilder->getRequestUpdatePage($request);
            if ($requestPosition !== null) {
                $pages []= $this->pageBuilder->getRequestPositionUpdatePage($requestPosition);
            }
            
            $pages [] = $this->getUpdatePageCommon($requestMessage);
        }             
        
        if ($this->isCreate()) {
            $requestPosition = $this->findRequestPositionOnGetParam();
            $request = $requestPosition->request;
            $pages []= $this->pageBuilder->getRequestUpdatePage($request);
            $pages []= $this->pageBuilder->getRequestPositionUpdatePage($requestPosition);
            $pages []= $this->getCreatePage();            
        }

        return $pages;
    }

    private function getUpdatePageCommon($requestMessage) 
    {
        return $this->pageBuilder->getRequestMessageUpdatePage($requestMessage);
    }
    
    private function findRequestOnGetParam()
    {
        $requestId = \Yii::$app->request->get('requests_request_id');
        if ($requestId === null) {
            return null;
        }
        
        $request = Request::findOne(['id' => $requestId]);
        if ($request === null) {
            throw new NotFoundHttpException();
        }
        
        return $request;
    }
    
    private function findRequestPositionOnGetParam()
    {
        $requestPositionId = \Yii::$app->request->get('requests_request_position_id');
        if ($requestPositionId === null) {
            return null;
        }
        
        $requestPosition = RequestPosition::findOne(['id' => $requestPositionId]);
        if ($requestPosition === null) {
            throw new NotFoundHttpException('Страница не найдена');
        }
        
        return $requestPosition;
    }
}