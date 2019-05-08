<?php

namespace detalika\requests\models\relation;

use yii\helpers\Url;

use detalika\requests\models\search\RequestMessageSearch;

class RequestMessageInRequestPositionSearch extends RequestMessageSearch 
{   
    use AdminNavigationFactoryTrait;
    
    public function formName()
    {
        return 'requestMessage';
    }
    
    public function getGridColumns()
    {
        $columns = parent::getGridColumns();
        // Нам не нужен запрос, так как сообщения все сообщения относятся к 
        // одному запросу.
        unset($columns['requests_request_id']);
        unset($columns['requests_request_position_id']);
        $columns['actions'] = $this->getActionsColumn($columns['actions']);    
        
        return $columns;
    }
    
    protected function getActionsColumn($actionColumn)
    {
        $navigationFactory = $this->getNavigationFactory();
        $routeItems = $navigationFactory->createRouteItems();
        $navigation = $navigationFactory->createNavigation();
        $requestMessageControllerId = $routeItems->getRequestMessageControllerId();
        
        // Для правильной работы ссылок внутри формы
        // формы редакитрования позиции.
        $actionColumn['controller'] = $requestMessageControllerId;
           
        $actionColumn['urlCreator'] = function ($action, $model, $key, $index) use ($requestMessageControllerId, $navigation) {
            if ($action === 'update') {
                $baseRoute = [$requestMessageControllerId . '/update', 'id' => $model->id]; 
                // Нам нужно указать, что мы переходим из позиции запроса.
                $route = $navigation->getRouteFromRequestPositionExisitng($baseRoute);
                return Url::to($route);
            } elseif ($action === 'delete') {
                return Url::to([$requestMessageControllerId . '/delete', 'id' => $model->id]);
            }
        };
        
        return $actionColumn;
    }
}