<?php

namespace detalika\requests\common\navigation;

use execut\navigation\behaviors\navigation\Page;

class UserPageBuilder extends AdminPageBuilder 
{
    public function __construct()
    {
        parent::__construct();
        $this->routeItems = new UserRouteItems();
        $this->navigation = new UserNavigation();
    }
    
    public function getClientCarIndexPageTitle()
    {
        return 'Гараж';
    }
        
    public function getRequestIndexPageTitle()
    {
        return 'Запросы';
    }
    
    public function getRequestMessageIndexPageTitle() 
    {
        return 'Сообщения запросов пользователя';
    }
    
    public function getRequestPositionIndexPageTitle()
    {
        return 'Позиции запросов пользователя';
    }
    
    public function getClientCarCreatePageTitle()
    {
        return 'Создать автомобиль';
    }
    
    public function getRequestViewPage($request)
    {
        $requestStatusName = $request->requestStatusName;
        $clientShortName = $request->clientCar->carShortName;
        $pageHeader = 'Запрос #' . $request->id 
            . ' на ' . $clientShortName . ' '
            . ' (' . $requestStatusName . ' )';
        
        return $this->getViewPage(
            $this->routeItems->getRequestControllerId(),
            $request,
            'titleString',
            $pageHeader
        );
    }

    public function getViewPage(
        $controllerId, 
        $model, 
        $titleAttribute = 'titleString',
        $pageHeader = null
    ) {
        $url = [
            $controllerId . '/view',
            'id' => $model->id,
        ];
        
        if ($this->isNeedAddExistingFromGetParamsToUrls) {
            $navigationPageKey = 'view';
            $url = $this->navigation->getRouteForPageWithExisting($url,  $controllerId, $navigationPageKey);
        }
        
        $pageTitle = $model->$titleAttribute;
        if ($pageHeader === null) {
            $pageHeader = $pageTitle;
        }
        
        return [
             'class' => Page::className(),
             'params' => [
                 'url' => $url,
                 'name' => $pageTitle,
                 'header' => $pageHeader,
                 'title' => $pageTitle,
            ],
        ];
    }
    
}