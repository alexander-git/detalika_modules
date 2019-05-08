<?php

namespace detalika\auth;

use yii\web\Controller;

use execut\actions\Action;
use execut\actions\action\adapter\GridView as GridViewAdapter;
use execut\actions\action\adapter\EditWithRelations as EditWithRelationsAdapter;
use execut\actions\action\adapter\Delete as DeleteAdapter;
use execut\actions\action\adapter\viewRenderer\DynaGrid as DynaGridViewRenderer;
use execut\navigation\behaviors\Navigation;
use execut\navigation\behaviors\navigation\Page;

abstract class CrudController extends Controller
{    
    public function behaviors()
    {
        return array_merge([
            'navigation' => [
                'class' => Navigation::className(),
                'pages' => $this->getPages(),
            ],
        ], parent::behaviors());
    }
    
    public function actions()
    {
        $searchModelClassName = $this->getSearchModelClassName();        
        return array_merge([
            'index' => [
                'class' => Action::className(),
                'adapter' => [
                    'class' => GridViewAdapter::className(),
                    'model' => $searchModelClassName,
                    'view' => [
                        'class' => DynaGridViewRenderer::className(),
                        'title' => $this->getGridViewTitle(),
                        'modelClass' => $searchModelClassName,
                    ],       
                    'attributes' =>  $this->getGridViewAdapterAttributesPropertyValue()
                ],
            ],
            'update' => [
                'class' => Action::className(),
                'adapter' => [
                    'class' => EditWithRelationsAdapter::className(),
                    'editAdapterConfig' => [
                        'modelClass' => $this->getEditModelClassName(),
                    ],
                ],
            ],
            'delete' => [
                'class' => Action::className(),
                'adapter' => [
                    'class' => DeleteAdapter::className(),
                    'modelClass' => $this->getDeleteModelClassName(),
                ],
            ],
        ], parent::actions()); 
    }  
    
    abstract protected function getIndexPageTitle();
    abstract protected function getSearchModelClassName();
    abstract protected function getEditModelClassName();
     
    protected function getMainPageTitle()
    {
        return 'Главная';
    }
    
    protected function getCreatePageTitle()
    {
        return 'Создать';
    }
    
    protected function getGridViewTitle()
    {
        return $this->getIndexPageTitle();
    }
    
    protected function getModelClassName() 
    {
        return $this->getEditModelClassName();
    }
       
    protected function getDeleteModelClassName()
    {
        return $this->getEditModelClassName();
    }
    
    protected function getTitleAttributeName()
    {
        return 'titleString';
    }
    
    protected function getIdAttributeName()
    {
        return 'id';
    }
    
    protected function getTextAttributeNameForAjaxSearch()
    {
        return 'name';
    }
    
    // Используется при поиске через ajax.
    protected function getGridViewAdapterAttributesPropertyValue()
    {
        return [
            $this->getIdAttributeName(),
            'text' => $this->getTextAttributeNameForAjaxSearch(),
        ];
    }
    
    protected function getPages()
    {
        $mainPageTitle = $this->getMainPageTitle();
        $indexPageTitle = $this->getIndexPageTitle();
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

        $actionId = $this->action->id;
        $idAttribute = $this->getIdAttributeName();
        $idValue = \Yii::$app->request->get($idAttribute);
        // Страница создания/обновления.
        if ($actionId === 'update') {
            $modelClass = $this->getModelClassName();
            $urlParams = ['/' . $this->getUniqueId() . '/update'];
            if ($idValue !== null) {
                $model = $modelClass::findOne([$idAttribute => $idValue]);
                $titleAttribute = $this->getTitleAttributeName();
                $pageTitle = $model->$titleAttribute;
                $urlParams[$idAttribute] = $idValue;
            } else {
                $pageTitle = $this->getCreatePageTitle();
            }
        
            $pages[] = [
                'class' => Page::className(),
                'params' => [
                    'url' => $urlParams,
                    'name' => $pageTitle,
                    'header' => $pageTitle,
                    'title' => $pageTitle,
                ],
            ];
        }
        
        return $pages;
    }     
}