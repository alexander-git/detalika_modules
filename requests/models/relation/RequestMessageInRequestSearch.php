<?php

namespace detalika\requests\models\relation;

use yii\helpers\Url;

use detalika\requests\models\search\RequestMessageSearch;

class RequestMessageInRequestSearch extends RequestMessageSearch 
{
    use AdminNavigationFactoryTrait;
    
    public function formName()
    {
        return 'requestMessage';
    }
    
    public function getGridColumns()
    {
        $columns = parent::getGridColumns();
        unset($columns['requests_request_id']);
        
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
        // редактирования запроса.
        $actionColumn['controller'] = $requestMessageControllerId;

        $actionColumn['urlCreator'] = function ($action, $model, $key, $index) use ($requestMessageControllerId, $navigation) {
            if ($action === 'update') {
                $baseRoute = [$requestMessageControllerId . '/update', 'id' => $model->id]; 
                // Нам нужно указать, что мы переходим из запроса.
                $route = $navigation->getRouteFromRequestExisitng($baseRoute);
                return Url::to($route);
            } elseif ($action === 'delete') {
                return Url::to([$requestMessageControllerId . '/delete', 'id' => $model->id]);
            }
        };
        
        return $actionColumn;
    }
}