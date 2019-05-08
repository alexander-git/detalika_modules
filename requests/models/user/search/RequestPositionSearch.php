<?php

namespace detalika\requests\models\user\search;

use yii\helpers\Html;

use detalika\requests\common\CommonUrls;
use detalika\requests\common\CurrentUser;
use detalika\requests\models\base\Request;
use detalika\requests\models\search\RequestPositionSearch as BaseRequestPositionSearch;

class RequestPositionSearch extends BaseRequestPositionSearch
{
    protected function addOptionalConditionsToQuery($query)
    {
        parent::addOptionalConditionsToQuery($query);
        
        $requestPositionTable =  self::tableName();
        
        $currentClientProfileId = CurrentUser::instance()->getClientProfileId();
        $requestIds = Request::getRequestIdsForClient($currentClientProfileId);
        
        $query->andFilterWhere(['in', $requestPositionTable . '.requests_request_id', $requestIds]);
        $query->andFilterWhere([$requestPositionTable. '.visible' => true]);
    }
    
    public function getGridColumns() 
    {
        $requestsUrl = CommonUrls::getUserRequestsUrlForAjaxList();
        
        $columns = parent::getGridColumns();
 
        $requestIdColumn = $columns['requests_request_id'];
        $requestIdColumn['filterWidgetOptions']['pluginOptions']['ajax']['url'] = $requestsUrl;

        $columns['requests_request_id'] = $requestIdColumn; 
        
        unset($columns['id']);
        unset($columns['parent_id']);
        unset($columns['name']);
        unset($columns['link_to_search']);
        unset($columns['goods_article_id']);
        unset($columns['goods_good_id']);
        unset($columns['good_name']);
        unset($columns['price']);
        unset($columns['quantity']);
        unset($columns['delivery_partner_id']);
        unset($columns['requestMessagesCountFilter']);
        unset($columns['visible']);
        unset($columns['updated']);  
        unset($columns['created']);
        unset($columns['actions']);
        
        // Сделаем столбец с полным именем первым.
        $newColumns = [];
        $newColumns['positionName'] = [
            'attribute' => 'positionName',
            'format' => 'raw',
            'value' => function($model, $key, $index) {
                $result = '';
                for ($i = 0; $i < $model->level; $i++) {
                    $result .= '&nbsp;';
                }
                
                /*
                if (!empty($model->pathstr)) {
                    $result .= $model->pathstr . '&nbsp;';
                }
                */
                
                $positionNameHtml = Html::tag(
                    'h3',
                    $model->positionName,
                    ['style' => 'font-weight : bold']
                );
                
                if (!empty($model->link_to_search)) {
                    $url = $model->link_to_search;
                    $result .= Html::a($positionNameHtml, $url, ['target' => '_blank']);
                } else {
                    $result .= $positionNameHtml;   
                }
                
                return $result;
            },
        ];
            
        foreach ($columns as $key => $value) {
            $newColumns[$key] = $value;
        }
        
        return $newColumns;   
    }
}
