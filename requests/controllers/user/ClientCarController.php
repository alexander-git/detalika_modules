<?php

namespace detalika\requests\controllers\user;

use yii\filters\AccessControl;

use detalika\requests\common\AccessCheck;
use detalika\requests\components\UserDynaGridViewRenderer;
use detalika\requests\controllers\ClientCarController as BaseClientCarController;
use detalika\requests\models\user\forms\ClientCarForm;
use detalika\requests\models\user\search\ClientCarSearch;

class ClientCarController extends BaseClientCarController
{
    use UserControllerTrait;
    
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['access'] =  [
            'class' =>  AccessControl::className(),
            'only' => ['index', 'update'],
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['index', 'update'],
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
        
        if (
            ($action->id === 'update' && $this->isUpdate()) ||
            ($action->id === 'delete')
        ) {
            $clientCar = $this->findModel();
            AccessCheck::instance()->checkCanCurrentUserEditClientCar($clientCar);
        }

        return true;
    }
    
    protected function getEditModelClassName()
    {
        return ClientCarForm::className();
    }
    
    protected function getSearchModelClassName() 
    {
        return ClientCarSearch::className();
    }
    
    public function actions()
    {
        $actions = parent::actions();
        $actions['index'] = $this->getModifiedIndexAction($actions['index']);
        $actions['delete'] = $this->getHideAction();
        
        return $actions;
    }
    
    private function getModifiedIndexAction($indexAction)
    {
        // Уберём все фильтры и ненужные кнопки.
        $indexAction['adapter']['view']['class'] = UserDynaGridViewRenderer::className();
        $indexAction['adapter']['view']['widget']['gridOptions']['filterModel'] = null;
        // Уберём заголовки всех столбцов.
        $indexAction['adapter']['view']['widget']['gridOptions']['showHeader'] = null;
        
        return $indexAction;
    }
}