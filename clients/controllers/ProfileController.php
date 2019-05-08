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
use detalika\clients\models\Profile;
use detalika\clients\forms\ProfileForm;
use yii\filters\AccessControl;


class ProfileController extends CrudController
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
        
        $indexPageTitle = 'Клиенты';
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
                    'model' => ProfileForm::className(),
                    'view' => [
                        'class' => DynaGridViewRenderer::className(),
                        'title' => 'Клиенты',
                        'modelClass' => ProfileForm::className(),
                    ],                    
                    'attributes' =>  [
                        'id',
                        'text' => 'fullName',
                    ],
                ],
            ],
            'update' => [
                'class' => Action::className(),
                'adapter' => [
                    'class' => EditWithRelationsAdapter::className(),
                    'modelClass' => Profile::className(),
                    //'view' => [
                    //   'class' => \execut\actions\action\adapter\viewRenderer\DetailView::className(),
                    //    'widget' => [
                    //        'class' => \detalika\clients\widgets\js\ProfileJS::className(),
                    //    ],
                    //],
                ],
            ],
            'delete' => [
                'class' => Action::className(),
                'adapter' => [
                    'class' => DeleteAdapter::className(),
                    'modelClass' => Profile::className(),
                ],
            ],
        ], parent::actions()); 
    }
        
    private function getUpdatePageConfig($id)
    {
        $model = Profile::findByPk($id);
        $name = $model->fullName;
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