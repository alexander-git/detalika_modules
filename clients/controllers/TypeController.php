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
use detalika\clients\models\Type;
use detalika\clients\forms\TypeForm;
use yii\filters\AccessControl;


class TypeController extends CrudController
{
    public function behaviors()
    {
        $mainPageTitle = 'Главная';
        $indexPageTitle = 'Типы клиентов';
        $pages = [
            [
                'class' => Page::className(),
                'params' => [
                    'url' => [
                        '/'
                    ],
                    'name' => $mainPageTitle,
                    'header' => $mainPageTitle,
                    'title' => $mainPageTitle,
                ],
            ],
            [
                'class' => Page::className(),
                'params' => [
                    'url' => [
                        '/' . $this->getUniqueId() . '/index',
                    ],
                    'name' => $indexPageTitle,
                    'header' => $indexPageTitle,
                    'title' => $indexPageTitle,
                ],
            ],
        ];

        $id = $this->getGet('id');
        if ($id !== null) {
            $model = Type::findByPk($id);
            $name = $model->name;
            $pages[] = [
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
                    'model' => TypeForm::className(),
                    'view' => [
                        'class' => DynaGridViewRenderer::className(),
                        'title' => 'Типы клиентов',
                        'modelClass' => TypeForm::className(),
                    ],                    
                ],
            ],
            'update' => [
                'class' => Action::className(),
                'adapter' => [
                    'class' => EditWithRelationsAdapter::className(),
                    'modelClass' => Type::className(),
                ],
            ],
            'delete' => [
                'class' => Action::className(),
                'adapter' => [
                    'class' => DeleteAdapter::className(),
                    'modelClass' => Type::className(),
                ],
            ],
        ], parent::actions()); 
    }     
}