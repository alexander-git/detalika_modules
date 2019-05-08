<?php

namespace detalika\requests\models\user\search;

use detalika\requests\common\CurrentUser;
use detalika\requests\helpers\KartikGridViewHelper;
use detalika\requests\models\base\Request;
use detalika\requests\models\search\RequestMessageSearch as BaseRequestMessageSearch;

class RequestMessageSearch extends BaseRequestMessageSearch
{
    protected function addOptionalConditionsToQuery($query)
    {
        parent::addOptionalConditionsToQuery($query);
        $currentClientProfileId = CurrentUser::instance()->getClientProfileId();
        $requestIds = Request::getRequestIdsForClient($currentClientProfileId);
        
        $requestMessageTable =  self::tableName();
        
        // Будем выводить сообщеия которые относятся к запросам 
        // пользователя.
        $query->andFilterWhere(['in', $requestMessageTable. '.requests_request_id', $requestIds]);
        $query->andFilterWhere([$requestMessageTable. '.visible' => true]);
    }
    
    public function getGridColumns() 
    {
        $columns = parent::getGridColumns();
        
        unset($columns['id']);
        unset($columns['visible']);
        unset($columns['updated']);
        
        $columns['actions'] = $this->getModifiedActionsColumn($columns['actions']);
        
        return $columns;
    }
    
    private function getModifiedActionsColumn($actionsColumn) 
    {
        $actionsColumn['buttons'] = [
            'update' => function ($url, $model, $key) {
                if (CurrentUser::instance()->hasRequestMessage($model)) {
                    return KartikGridViewHelper::getUpdateButtonHtml($url);
                } else {
                    return '';
                }
            }, 
            'delete' => function ($url, $model, $key) {
                if (CurrentUser::instance()->hasRequestMessage($model)) {
                    return KartikGridViewHelper::getDeleteButtonHtml($url);
                } else {
                    return '';
                }
            },        
        ];
            
        return $actionsColumn;
    }
}