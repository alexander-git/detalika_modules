<?php

namespace detalika\requests\controllers;

use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;

use kartik\detail\DetailView;

use detalika\requests\common\AccessControlTools;
use detalika\requests\AdvancedCrudController;
use detalika\requests\models\forms\ClientCarForm;
use detalika\requests\models\search\ClientCarSearch;

class ClientCarController extends AdvancedCrudController
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
    
    public function actions()
    {
        
        return ArrayHelper::merge(parent::actions(), [
            'update' => [
                'adapter' => [
                    'editAdapterConfig' => [
                        'view' => [
                            'widget' => [
                                'mode' => DetailView::MODE_EDIT,
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }
        
    protected function getEditModelClassName()
    {
        return ClientCarForm::className();
    }
    
    protected function getSearchModelClassName() 
    {
        return ClientCarSearch::className();
    }
    
    protected function getModelsListTitle() 
    {
        return $this->pageBuilder->getClientCarIndexPageTitle();
    }
            
    protected function getTextAttributeNameForAjaxSearch()
    {
        return 'carFullName';
    }
        
    protected function getCreatePageTitle() 
    {
        return $this->pageBuilder->getClientCarCreatePageTitle();
    }
}