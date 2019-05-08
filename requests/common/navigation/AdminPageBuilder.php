<?php

namespace detalika\requests\common\navigation;

use execut\navigation\behaviors\navigation\Page;

class AdminPageBuilder
{
    protected $routeItems;
    protected $navigation;
    protected $isNeedAddExistingFromGetParamsToUrls;
    
    public function __construct($isNeedAddExistingFromGetParamsToUrls = true)
    {
        $this->routeItems = new AdminRouteItems();
        $this->navigation = new AdminNavigation();
        $this->isNeedAddExistingFromGetParamsToUrls = $isNeedAddExistingFromGetParamsToUrls;
    }
    
    public function getMainPageTitle()
    {
        return 'Главная';
    }
    
    public function getClientCarIndexPageTitle()
    {
        return 'Автомобили клиентов';
    }
    
    public function getRequestIndexPageTitle()
    {
        return 'Запросы';
    }
    
    public function getRequestPositionIndexPageTitle()
    {
        return 'Позиции запросов';
    }
    
    public function getRequestMessageIndexPageTitle()
    {
        return 'Сообщения запросов';
    }
    
    public function getRequestStatusIndexPageTitle()
    {
        return 'Статусы запросов';
    }
    
    public function getRequestPositionStatusIndexPageTitle()
    {
        return 'Статусы позиций запросов';
    }
    
    public function getCreatePageTitleDefault()
    {
        return 'Создать';
    }
    
    public function getMassCreatePageTitleDefault()
    {
        return 'Создать';
    }
    
    public function getClientCarCreatePageTitle()
    {
        return 'Созадать автомобиль клиента';
    }
    
    public function getRequestCreatePageTitle()
    {
        return 'Создать запрос';
    }
    
    public function getRequestPositionCreatePageTitle()
    {
        return 'Создать позицию запроса';
    }
    
    public function getRequestMessageCreatePageTitle()
    {
        return 'Создать сообщение запроса';
    }
    
    public function getRequestStatusCreatePageTitle()
    {
        return 'Создать статус запроса';
    }
    
    public function getRequestPositionStatusCreatePageTitle()
    {
        return 'Созадьб статус позиции запроса';
    }
    
    public function getRequestPositionMassCreatePageTitle()
    {
        return 'Создать позиции запроса';
    }
    
    public function getMainPage()
    {
        $mainPageTitle = $this->getMainPageTitle();
        $url = ['/'];
        if ($this->isNeedAddExistingFromGetParamsToUrls) {
            $navigationPageKey = 'main';
            $url = $this->navigation->getRouteForPageWithExisting($url, null, $navigationPageKey);
        }
        
        return  [
            'class' => Page::className(),
            'params' => [
                'url' => $url,
                'name' => $mainPageTitle,
                'header' => $mainPageTitle,
                'title' => $mainPageTitle,
            ],
        ];
    }
    
    public function getIndexPage(
        $controllerId, 
        $pageTitle, 
        $pageHeader = null, 
        $breadcrumbName = null
    ) {
        $url = [$controllerId . '/index'];
        
        if ($this->isNeedAddExistingFromGetParamsToUrls) {
            $navigationPageKey = 'index';
            $url = $this->navigation->getRouteForPageWithExisting($url, $controllerId, $navigationPageKey);
        }   
        
        if ($pageHeader === null) {
            $pageHeader = $pageTitle;
        }
        
        if ($breadcrumbName === null) {
            $breadcrumbName = $pageTitle;
        }
        
        return [
             'class' => Page::className(),
             'params' => [
                 'url' => $url,
                 'name' => $breadcrumbName,
                 'header' => $pageHeader,
                 'title' => $pageTitle,
            ],
         ];
    }
    
    public function getCreatePage($controllerId, $pageTitle = null)
    {
        $url = [$controllerId . '/update'];
        
        if ($this->isNeedAddExistingFromGetParamsToUrls) {
            $navigationPageKey = 'create';
            $url = $this->navigation->getRouteForPageWithExisting($url, $controllerId, $navigationPageKey);
        }
        
        if ($pageTitle === null) {
            $pageTitle = $this->getCreatePageTitleDefault();
        }
        
        return [
             'class' => Page::className(),
             'params' => [
                 'url' => $url,
                 'name' => $pageTitle,
                 'header' => $pageTitle,
                 'title' => $pageTitle,
            ],
         ];
    }
        
    public function getUpdatePage(
        $controllerId, 
        $model, 
        $titleAttribute = 'titleString'
    ) {
        $url = [
            $controllerId . '/update',
            'id' => $model->id,
        ];
        
        if ($this->isNeedAddExistingFromGetParamsToUrls) {
            $navigationPageKey = 'update';
            $url = $this->navigation->getRouteForPageWithExisting($url,  $controllerId, $navigationPageKey);
        }
        
        $pageTitle = $model->$titleAttribute;
        return [
             'class' => Page::className(),
             'params' => [
                 'url' => $url,
                 'name' => $pageTitle,
                 'header' => $pageTitle,
                 'title' => $pageTitle,
            ],
         ];
    }
    
    public function getClientCarIndexPage()
    {
        return $this->getIndexPage( 
            $this->routeItems->getClientCarControllerId(),    
            $this->getClientCarIndexPageTitle()
        );
    }
    
    public function getRequestIndexPage()
    {
        return $this->getIndexPage( 
            $this->routeItems->getRequestControllerId(),    
            $this->getRequestIndexPageTitle()
        );
    }
    
    public function getRequestPositionIndexPage()
    {
        return $this->getIndexPage(
            $this->routeItems->getRequestPositionControllerId(),
            $this->getRequestPositionIndexPageTitle()  
        );
    }
    
    public function getRequestMessageIndexPage()
    {
        return $this->getIndexPage(
            $this->routeItems->getRequestMessageControllerId(),
            $this->getRequestMessageIndexPageTitle()
        );
    }
    
    public function getRequestStatusIndexPage()
    {
        return $this->getIndexPage(
            $this->routeItems->getRequestStatusControllerId(),
            $this->getRequestStatusIndexPageTitle()
        );
    }
    
    public function getRequestPositionStatusIndexPage()
    {
        return $this->getIndexPage(
            $this->routeItems->getRequestPositionStatusControllerId(),
            $this->getRequestPositionStatusIndexPageTitle()
        );
    }
        
    public function getClientCarUpdatePage($clientCar)
    {
        return $this->getUpdatePage(
            $this->routeItems->getClientCarControllerId(),
            $clientCar
        );
    }
    
    public function getRequestUpdatePage($request)
    {
        return $this->getUpdatePage(
            $this->routeItems->getRequestControllerId(),
            $request
        );
    }
    
    public function getRequestPositionUpdatePage($requestPosition)
    {
        return $this->getUpdatePage(
            $this->routeItems->getRequestPositionControllerId(),
            $requestPosition
        );
    }
    
    public function getRequestMessageUpdatePage($requestMessage)
    {
        return $this->getUpdatePage(
            $this->routeItems->getRequestMessageControllerId(),
            $requestMessage
        );
    }
    
    public function getRequestStatusUpdatePage($requestStatus)
    {
        return $this->getUpdatePage(
            $this->routeItems->getRequestStatusControllerId(),
            $requestStatus
        );
    }
    
    public function getRequestPositionStatusUpdatePage($requestPositionStatus)
    {
        return $this->getUpdatePage(
            $this->routeItems->getRequestPositionStatusControllerId(),
            $requestPositionStatus
        );
    }
    
    public function getRequestPositionMassCreatePage()
    {
        return $this->getMassCreatePage(
            $this->routeItems->getRequestPositionControllerId(),
            $this->getRequestPositionMassCreatePageTitle()
        );
    }
    
    public function getRequestIndexPageWhenMoveFromClientCar($clientCar)
    {
        $clientCarShortName = $clientCar->carShortName; 
        $breadcrumbName = $this->getRequestIndexPageTitle();
        $pageHeader = $breadcrumbName . ' (' . $clientCarShortName .')';
        $pageTitle = $pageHeader;
        
        return $this->getIndexPage(
            $this->routeItems->getRequestMessageControllerId(),
            $pageTitle,
            $pageHeader,
            $breadcrumbName
        );
    }
    
    
    private function getMassCreatePage($controllerId, $pageTitle = null)
    {
        $url = [$controllerId . '/mass-create'];
        
        if ($this->isNeedAddExistingFromGetParamsToUrls) {
            $navigationPageKey = 'mass-create';
            $url = $this->navigation->getRouteForPageWithExisting($url, $controllerId, $navigationPageKey);
        }
        
        if ($pageTitle === null) {
            $pageTitle = $this->getMassCreatePageTitleDefault();
        }
        
        return [
             'class' => Page::className(),
             'params' => [
                 'url' => $url,
                 'name' => $pageTitle,
                 'header' => $pageTitle,
                 'title' => $pageTitle,
            ],
         ];
    }    
}