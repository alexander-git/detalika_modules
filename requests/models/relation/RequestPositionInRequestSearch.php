<?php

namespace detalika\requests\models\relation;

use yii\helpers\Url;

use detalika\requests\models\search\RequestPositionSearch;

class RequestPositionInRequestSearch extends RequestPositionSearch
{
    use AdminNavigationFactoryTrait;
    
    public function formName()
    {
        return 'requestPosition';
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
        $requestPositionControllerId = $routeItems->getRequestPositionControllerId();
        
        // Для правильной работы ссылок внутри формы
        // редактирования запроса.
        $actionColumn['controller'] = $requestPositionControllerId;
        
        $actionColumn['urlCreator'] = function ($action, $model, $key, $index) use ($requestPositionControllerId, $navigation) {
            if ($action === 'update') {
                $baseRoute = [$requestPositionControllerId . '/update', 'id' => $model->id]; 
                // Нам нужно указать, что мы переходим из запроса.
                $route = $navigation->getRouteFromRequestExisitng($baseRoute);
                return Url::to($route);
            } elseif ($action === 'delete') {
                return Url::to([$requestPositionControllerId . '/delete', 'id' => $model->id]);
            }
        };

        return $actionColumn;
    }
}