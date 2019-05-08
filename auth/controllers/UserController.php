<?php

namespace detalika\auth\controllers;

use detalika\auth\models\crud\UserSearch;

use yii\web\Controller;
use execut\actions\Action;
use execut\actions\action\adapter\GridView as GridViewAdapter;
use execut\actions\action\adapter\viewRenderer\DynaGrid as DynaGridViewRenderer;

class UserController extends Controller
{

    public function getTextAttributeNameForAjaxSearch()
    {
        return 'email';
    }
     
    
    public function actions()
    {
        return [
            'index' => [
                'class' => Action::className(),
                'adapter' => [
                    'class' => GridViewAdapter::className(),
                    'model' => UserSearch::className(),
                    'view' => [
                        'class' => DynaGridViewRenderer::className(),
                        'title' => 'Пользователи',
                        'modelClass' => UserSearch::className(),
                    ],       
                    'attributes' => [
                        'id',
                        'text' => $this->getTextAttributeNameForAjaxSearch(),
                    ],
                ],
            ],
        ];   
    }
    
}