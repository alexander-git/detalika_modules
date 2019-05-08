<?php

namespace detalika\requests\controllers\user;

use Yii;
use yii\filters\AccessControl;

use kartik\detail\DetailView;

use detalika\requests\common\AccessCheck;
use detalika\requests\components\UserDynaGridViewRenderer;
use detalika\requests\components\requestPosition\RequestPositionUserDynaGridViewRenderer;
use detalika\requests\controllers\RequestController as BaseRequestController;
use detalika\requests\models\user\forms\RequestForm;
use detalika\requests\models\user\search\RequestSearch;

class RequestController extends BaseRequestController
{
    use UserControllerTrait;
    
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['access'] =  [
            'class' =>  AccessControl::className(),
            'only' => ['index', 'update', 'view'],
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['index', 'update', 'view'],
                    'roles' => ['@'],
                ],
            ],
        ];
       
        return $behaviors;
    }
    
    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }
        
        $actionId = $action->id;
        
        if ($actionId === 'update' && $this->isUpdate()) {
            if (!AccessCheck::instance()->canCurrentUserPicking()) {
                $idAttribute = $this->getIdAttributeName();
                $idValue = $this->getIdAttributeValue();
                $baseRoute = ['view',  $idAttribute  => $idValue];
                $route = $this->navigation->getRouteWithExisting($baseRoute);
                return $this->redirect($route);
            }
        }
        
        if ($actionId === 'view') {    
            $request = $this->findModel();
            AccessCheck::instance()->checkCanCurrentUserEditRequest($request);
        }
        
        return true;
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
    
    public function actions()
    {
        $actions = parent::actions();
        
        $actions['index'] = $this->getModifiedIndexAction($actions['index']);
        $actions['update'] = $this->getModifiedUpdateAction($actions['update']);
        // Действие view сделаем на основе update, но отключим возможность редактирования.
        $actions['view'] = $this->getViewAction($actions['update']);
        
        // Пользоветель не может удалять запросы.
        unset($actions['delete']);
        
        return $actions;
    }
    
    private function getModifiedIndexAction($indexAction)
    {
          // Уберём все фильтры и ненужные кнопки.
        $indexAction['adapter']['view']['class'] = UserDynaGridViewRenderer::className();
        $indexAction['adapter']['view']['widget']['gridOptions']['filterModel'] = null;  
        // Уберём заголовки всех столбцов.
        $indexAction['adapter']['view']['widget']['gridOptions']['showHeader'] = null;
        
        
        // Изменим кнопку добавления, так чтобы при переходе из автомобиля этот 
        // автомобиль был установлен при создании запроса по умолчанию. 
        $get = Yii::$app->request->queryParams;
        $baseUrlAttributes = [];
        if (isset($get['requests_client_car_id'])) {
            $baseUrlAttributes['requests_client_car_id'] = $get['requests_client_car_id'];                
        }
        $urlAttributes = $this->navigation->getRouteWithExisting($baseUrlAttributes);
        $indexAction['adapter']['view']['urlAttributes'] = $urlAttributes;
        
        return $indexAction;
    }
    
    private function getModifiedUpdateAction($updateAction)
    {
        if (isset($updateAction['adapter']['editAdapterConfig'])) {
           $editAdapterConfig = $updateAction['adapter']['editAdapterConfig'];    
        } else {
           $editAdapterConfig = [];
        }
        
        // Чтобы при создании запросов при переходе из автомобиля, id 
        // автомобиля был сразу установлен.
        $editAdapterConfig['additionalAttributes'] = [
            'requests_client_car_id',
        ];
        
        if (AccessCheck::instance()->canCurrentUserPicking()) {
            $editAdapterConfig['view']['widget']['mode'] = DetailView::MODE_EDIT; 
        }
        
        $updateAction['adapter']['editAdapterConfig'] = $editAdapterConfig;
        
        // Уберём все фильтры и ненужные кнопки.
        // Поставим RequestPositionUserDynaGridViewRenderer для возможности 
        // менять строку в GridView при выводе позиции.
        $updateAction['adapter']['relationAdapterConfig']['requestPositionsInRequest']
            ['view']['class'] = RequestPositionUserDynaGridViewRenderer::className();
        // Уберём кнопку добавить.
        $updateAction['adapter']['relationAdapterConfig']['requestPositionsInRequest']
            ['view']['widget']['gridOptions']['toolbar'] = null;
        
        // Уберём список сообщений относящихся к запросу и оставим только позиции.
        unset($updateAction['adapter']['relationAdapterConfig']['requestMessagesInRequest']);
        $updateAction['adapter']['relations'] = [
            'requestPositionsInRequest',
        ];
                
        return $updateAction;
    }
    
    private function getViewAction($updateAction)
    {
        $viewAction = $updateAction;       
        if (!isset($viewAction['adapter']['editAdapterConfig'])) {
            $viewAction['adapter']['editAdapterConfig'] = [];
        }
        
        //$viewAction['adapter']['editAdapterConfig']['view']['widget']['mode'] = DetailView::MODE_VIEW; 
        
        // Отключим для пользователя возможность редактирования.
        $viewAction['adapter']['editAdapterConfig']['view']['widget']['enableEditMode'] = false;  
        // Скроем виджет таким хитрым способом. По умолочанию mainTemplate = "{detail}", сделем его пустым.
        $viewAction['adapter']['editAdapterConfig']['view']['widget']['mainTemplate'] = ""; 

        return $viewAction;
    }
    
    protected function getPages()
    {
        $actionId = $this->action->id;
        
        // Страница создания/обновления.
        if ($actionId === 'view') {          
            if ($this->navigation->isMoveFromClientCarPage()) {
                $request = $this->findModel(); 
                $pages = [];
                $pages [] = $this->getMainPage();
                $pages [] = $this->getIndexPage();
                $pages [] = $this->pageBuilder->getClientCarUpdatePage($request->clientCar);
                $pages[] = $this->getViewPageCommon($request);
                return $pages;
            } else {
                $pages = [];
                $pages [] = $this->getMainPage();
                $pages [] = $this->getIndexPage();
                $pages[] = $this->getViewPage();  
                return $pages;
            }
        }
        
        return parent::getPages();
    }
    
    protected function getViewPage()
    {
        $request = $this->findModel();
        return $this->getViewPageCommon($request);
    }

    private function getViewPageCommon($request)
    {
        return $this->pageBuilder->getRequestViewPage($request);
    }
}