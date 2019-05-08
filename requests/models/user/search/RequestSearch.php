<?php

namespace detalika\requests\models\user\search;

use Yii;
use yii\helpers\Html;

use detalika\requests\common\CurrentUser;
use detalika\requests\models\base\ClientCar;
use detalika\requests\models\search\RequestSearch as BaseRequestSearch;

class RequestSearch extends BaseRequestSearch
{        
    const CREATED_DATE_FORMAT_YII = 'php:d F Y';
    
    protected function addOptionalConditionsToQuery($query)
    {
        parent::addOptionalConditionsToQuery($query);
        
        $requestTable =  self::tableName();
        $clientCarTable = ClientCar::tableName();
        
        $currentClientProfileId = CurrentUser::instance()->getClientProfileId();
        $query->andFilterWhere([$requestTable . '.clients_profile_id' => $currentClientProfileId]);
        $query->andFilterWhere([$requestTable . '.visible' => true]);
        // Отображаем только те запросы, которые нпринадлежат к видимым машинам.
        $query->andFilterWhere([$clientCarTable . '.visible' => true]);
    }
    
    protected function modifyDataProviderConfig($baseDataProvderConfig)
    {
        $config = parent::modifyDataProviderConfig($baseDataProvderConfig);
        $config['sort']['defaultOrder'] = ['created' => SORT_DESC];
        return $config;
    }
    
    public function getGridColumns() 
    {
        $columns = parent::getGridColumns();
        unset($columns['visible']);
        unset($columns['clientNameFilter']);
        unset($columns['requestPositionsCountFilter']);
        unset($columns['requestMessagesCountFilter']);
        unset($columns['updated']);
        unset($columns['actions']);
        
        
        $columns['id'] = $this->getModifiedIdColumn($columns['id']);
        $columns['requests_request_status_id'] = $this->getModifiedRequestStatusIdColumn($columns['requests_request_status_id']);
        $columns['clientCarNameFilter'] = $this->getModifiedClientCarNameFilterColumn($columns['clientCarNameFilter']);
        
        // Более-менее нормальный способ.
        //$columns['created'] = $this->getCreatedColumn(self::CREATED_DATE_FORMAT, self::CREATED_DATE_FORMAT_YII);
        // Кривой способ.
        $columns['created'] = $this->getModifiedCreatedColumn($columns['created']);
                
        // Расположим столбцы в нужном порядке. 
        $columnsOrder = ['created', 'id', 'requests_request_status_id', 'clientCarNameFilter'];
        $newColumns = [];
        foreach ($columnsOrder as $key) {
            $newColumns[$key] = $columns[$key];
        }
        
        return $newColumns;        
    } 
    
    private function getModifiedIdColumn($column)
    {
        $that = $this;
        $column['format'] = 'raw';
        $column['value'] = function($model, $key, $index, $column) use ($that) {
            $text = 'Запрос №' . $model->id;
            return $that->getHtmlLinkToRequest($text, $model);
        };
        
        return $column;
    }
    
    private function getModifiedRequestStatusIdColumn($column)
    {
        $that = $this;
        $column['value'] = function($model, $key, $index, $column) use ($that) {
            $text = $model->requestStatusName;
            return $that->getHtmlLinkToRequest($text, $model);
        };
        
        return $column;
    }
    
    private function getModifiedClientCarNameFilterColumn($column)
    {
        $that = $this;
        $column['format'] = 'raw';
        $column['value'] = function($model, $key, $index, $column) use ($that) {
            $text = $model->clientCarFullName;
            return $that->getHtmlLinkToRequest($text, $model);
        };
        
        return $column;
    }
    
    private function getModifiedCreatedColumn($column)
    {
        $that = $this;
        $column['format'] = 'raw';
        $column['value'] = function($model, $key, $index, $column) use ($that) {
            $text = Yii::$app->formatter->format($model->created, ['date', self::CREATED_DATE_FORMAT_YII]);
            return $that->getHtmlLinkToRequest($text, $model);
        };
        
        return $column;
    }
    
    private static function getHtmlLinkToRequest($text, $request)
    {
        $route = ['update', 'id' => $request->id];
        return Html::a(Html::encode($text), $route);
    }
    
}