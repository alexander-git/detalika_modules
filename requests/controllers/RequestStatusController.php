<?php

namespace detalika\requests\controllers;

use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;

use detalika\requests\common\AccessControlTools;
use detalika\requests\AdvancedCrudController;
use detalika\requests\models\forms\RequestStatusForm;
use detalika\requests\models\search\RequestStatusSearch;

class RequestStatusController extends AdvancedCrudController
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
                            $availbleAjaxActions = ['index'];
                            return AccessControlTools::adminMatchCallback($rule, $action, $availbleAjaxActions);
                        },
                    ],
                ],
            ],
        ]);
    }
        
    protected function getEditModelClassName()
    {
        return RequestStatusForm::className();
    }
    
    protected function getSearchModelClassName() 
    {
        return RequestStatusSearch::className();
    }
    
    protected function getModelsListTitle() 
    {
        return $this->pageBuilder->getRequestStatusIndexPageTitle();
    }   
    
    protected function getCreatePageTitle() 
    {
        return $this->pageBuilder->getRequestStatusCreatePageTitle();
    }
}