<?php

namespace detalika\requests;

use yii\web\Controller;

use execut\actions\Action;
use execut\actions\action\adapter\GridView as GridViewAdapter;
use execut\actions\action\adapter\EditWithRelations as EditWithRelationsAdapter;
use execut\actions\action\adapter\Delete as DeleteAdapter;
use execut\navigation\behaviors\Navigation;
use execut\navigation\behaviors\navigation\Page;

use detalika\requests\components\DynaGridViewRenderer;

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
                        'title' => $this->getModelsListTitle(),
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
   
    abstract protected function getSearchModelClassName();
    abstract protected function getEditModelClassName();
    abstract protected function getModelsListTitle();  
    
    protected function getMainPageTitle()
    {
        return 'Главная';
    }
    
    protected function getCreatePageTitle()
    {
        return 'Создать';
    }
    
    protected function getIndexPageTitle()
    {
        return $this->getModelsListTitle();
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
        $pages = [];
        $pages []= $this->getMainPage();
        $pages []= $this->getIndexPage();
        
        $actionId = $this->action->id;

        // Страница создания/обновления.
        if ($actionId === 'update') {
            $pages[] = $this->getCreateUpdatePage();
        }
        
        return $pages;
    }     
    
    protected function getMainPage()
    {
        $mainPageTitle = $this->getMainPageTitle();
        return  [
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
    }
    
    protected function getIndexPage()
    {
        $indexPageTitle = $this->getIndexPageTitle();
        return [
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
    }
    
    
    protected function getCreateUpdatePage()
    {
        if ($this->isCreate()) {
            return $this->getCreatePage();
        } else {
            return $this->getUpdatePage();
        }
    }
    
    protected function getCreatePage()
    {
        $urlParams = ['/' . $this->getUniqueId() . '/update'];
        $pageTitle = $this->getCreatePageTitle();
        return [
            'class' => Page::className(),
            'params' => [
                'url' => $urlParams,
                'name' => $pageTitle,
                'header' => $pageTitle,
                'title' => $pageTitle,
            ],
        ];
    }
    
    protected function getUpdatePage()
    {
        $idAttribute = $this->getIdAttributeName();
        $idValue = \Yii::$app->request->get($idAttribute);
        
        $urlParams = ['/' . $this->getUniqueId() . '/update'];
        $urlParams[$idAttribute] = $idValue;
        
        $model = $this->findModel();
        $titleAttribute = $this->getTitleAttributeName();
        $pageTitle = $model->$titleAttribute;
        return [
            'class' => Page::className(),
            'params' => [
                'url' => $urlParams,
                'name' => $pageTitle,
                'header' => $pageTitle,
                'title' => $pageTitle,
            ],
        ];
    }
    
    final protected function getIdAttributeValue()
    {
        $idAttribute = $this->getIdAttributeName();
        return \Yii::$app->request->get($idAttribute, null);
    }
    
    protected function findModel()
    {
        $idAttribute = $this->getIdAttributeName();
        $idValue = $this->getIdAttributeValue();
        $modelClass = $this->getModelClassName();
        return $modelClass::findOne([$idAttribute => $idValue]);
    }
    
    
    final protected function isIndex()
    {
        return $this->action->id === 'index';
    }
    
    final protected function isCreate()
    {
        $actionId = $this->action->id;
        $idValue = $this->getIdAttributeValue();
        return ($actionId === 'update') && ($idValue === null);
    }
    
    final protected function isUpdate()
    {
        $actionId = $this->action->id;
        $idValue = $this->getIdAttributeValue();
        return ($actionId === 'update') && ($idValue !== null);
    }
}