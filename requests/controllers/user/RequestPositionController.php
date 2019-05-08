<?php

namespace detalika\requests\controllers\user;

use yii\filters\AccessControl;

use detalika\requests\components\requestPosition\RequestPositionUserDynaGridViewRenderer;
use detalika\requests\controllers\RequestPositionController as BaseRequestPositionController;
use detalika\requests\models\user\search\RequestPositionSearch;

class RequestPositionController extends BaseRequestPositionController
{
    use UserControllerTrait;
    
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['access'] =  [
            'class' =>  AccessControl::className(),
            'only' => ['index', 'parent-search'],
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['index', 'parent-search'],
                    'roles' => ['@'],
                ],
            ],
        ];
       
        return $behaviors;
    }

    protected function getSearchModelClassName() 
    {
        return RequestPositionSearch::className();
    }
    
    protected function getModelsListTitle()
    {
        return $this->pageBuilder->getRequestPositionIndexPageTitle();
    }
    
    public function actions()
    {
        $actions = parent::actions();
        
        $actions['index'] = $this->getModifiedIndexAction($actions['index']);

        // Возможность редактировать позиции для пользователя убираем.
        unset($actions['update']);
        unset($actions['delete']);
        
        // Возмлжность массоваго создания позиций для пользователя уберём.
        unset($actions['mass-create']);
        
        return $actions;
    }
    
    private function getModifiedIndexAction($indexAction)
    {
        // Уберём все фильтры и ненужные кнопки.
        // Для возможности менять строку в GridView при выводе позиции.
        $indexAction['adapter']['view']['class'] = RequestPositionUserDynaGridViewRenderer::className();
        $indexAction['adapter']['view']['widget']['gridOptions']['filterModel'] = null;
        // Уберём заголовки всех столбцов.
        $indexAction['adapter']['view']['widget']['gridOptions']['showHeader'] = null;
        // Уберём кнопку добавить.
        $indexAction['adapter']['view']['widget']['gridOptions']['toolbar'] = null;
        
        
        // Уберём заголовок у GridView.
        $indexAction['adapter']['view']['title'] = null;
        // Если нужно убрать и слово "список" из заголовка.
        //$indexAction['adapter']['view']['widget']['gridOptions']['panel']['heading'] = null;
        
        return $indexAction;
    }
}