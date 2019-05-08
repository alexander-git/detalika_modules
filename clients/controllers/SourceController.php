<?php

namespace detalika\clients\controllers;

use execut\actions\Action;
use execut\actions\action\adapter\GridView as GridViewAdapter;
use execut\actions\action\adapter\EditWithRelations as EditWithRelationsAdapter;
use execut\actions\action\adapter\Delete as DeleteAdapter;
use execut\actions\action\adapter\viewRenderer\DynaGrid as DynaGridViewRenderer;
use execut\navigation\behaviors\Navigation;
use execut\navigation\behaviors\navigation\Page;

use detalika\clients\CrudController;
use detalika\clients\models\Source;
use detalika\clients\forms\SourceForm;
use yii\filters\AccessControl;


class SourceController extends CrudController
{
    public function behaviors()
    {
        $pages = [];
        
        $mainPageTitle = 'Главная';
        $pages[] = [
            'class' => Page::className(),
            'params' => [
                'url' => [
                    '/'
                ],
                'name' => $mainPageTitle,
                'header' => $mainPageTitle,
                'title' => $mainPageTitle,
            ],
        ];
        
        $indexPageTitle = 'Источники клиентов';
        $pages[] = [
            'class' => Page::className(),
            'params' => [
                'url' => [
                    '/' . $this->getUniqueId() . '/index',
                ],
                'name' => $indexPageTitle,
                'header' => $indexPageTitle,
                'title' => $indexPageTitle,
            ],  
        ];

        $actionId = $this->action->id;
        $id = $this->getGet('id');

        if ($actionId === 'update' && $id !== null) {
            $pages[] = $this->getUpdatePageConfig($id);
        }

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
                        'roles'         => [$this->module->adminRole],
                    ]
                ],
            ],
        ], parent::behaviors());
    }
        
    public function actions()
    {
        return array_merge([
            'index' => [
                'class' => Action::className(),
                'adapter' => [
                    'class' => GridViewAdapter::className(),
                    'model' => SourceForm::className(),
                    'view' => [
                        'class' => DynaGridViewRenderer::className(),
                        'title' => 'Источники клиентов',
                        'modelClass' => SourceForm::className(),
                    ],                    
                    'attributes' =>  [
                        'id',
                        'text' => 'name',
                    ],
                ],
            ],
            'update' => [
                'class' => Action::className(),
                'adapter' => [
                    'class' => EditWithRelationsAdapter::className(),
                    'modelClass' => Source::className(),
                    //'view' => [
                    //   'class' => \execut\actions\action\adapter\viewRenderer\DetailView::className(),
                    //    'widget' => [
                    //        'class' => \detalika\clients\widgets\js\SourceJS::className(),
                    //    ],
                    //],
                ],
            ],
            'delete' => [
                'class' => Action::className(),
                'adapter' => [
                    'class' => DeleteAdapter::className(),
                    'modelClass' => Source::className(),
                ],
            ],
        ], parent::actions()); 
    }
        
    private function getUpdatePageConfig($id)
    {
        $model = Source::findByPk($id);
        $name = $model->name;
        return [
            'class' => Page::className(),
            'params' => [
                'url' => [
                    '/' . $this->getUniqueId() . '/update',
                    'id' => $id,
                ],
                'name' => $name,
                'header' => $name,
                'title' => $name,
            ],
        ];
    }
}