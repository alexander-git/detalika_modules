<?php

namespace detalika\requests;

use yii\web\NotFoundHttpException;

abstract class AdvancedCrudController extends CrudController 
{
    /**
     * @var \detalika\requests\common\navigation\AdminRouteItems
     */
    protected $routeItems;
    
    /**
     * @var \detalika\requests\common\navigation\AdminNavigation
     */
    protected $navigation;
    
    /**
     * @var \detalika\requests\common\navigation\AdminPageBuilder
     */
    protected $pageBuilder;
    
    public function init()
    {
        parent::init();
        $navigationFactory = $this->getNavigationFactory();
        $this->routeItems = $navigationFactory->createRouteItems(); 
        $this->navigation = $navigationFactory->createNavigation();
        $this->pageBuilder = $navigationFactory->createPageBuilder();
    }
    
    /**
     * @var \detalika\requests\common\navigation\AbstractNavigationFactory
     */
    abstract protected function getNavigationFactory();
    
    ////////////////////////////////////////////////////////////////////////////
    // Переопределим методы получения страниц для навигации, так чтобы всё 
    // работало черз pageBuilder.
    ////////////////////////////////////////////////////////////////////////////
    protected function getMainPage()
    {
        return $this->pageBuilder->getMainPage();
    }
    
    protected function getIndexPage()
    {
        $controllerId = $this->id;
        $pageTitle = $this->getIndexPageTitle();
        return $this->pageBuilder->getIndexPage($controllerId, $pageTitle);
    }
    
    protected function getCreatePage()
    {
        $pageTitle = $this->getCreatePageTitle();
        $controllerId = $this->id;
        return $this->pageBuilder->getCreatePage($controllerId, $pageTitle);
    }
    
    protected function getUpdatePage()
    {
        $controllerId = $this->id;
        $model = $this->findModel();
        return $this->pageBuilder->getUpdatePage($controllerId, $model);
    }
    
    protected function findModel() 
    {
        $model = parent::findModel();
        if ($model === null) {
            throw new NotFoundHttpException('Страница не найдена');
        }
        
        return $model;
    }
}