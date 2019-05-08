<?php
namespace detalika\requests\models\search;

use yii\helpers\Url;
use yii\helpers\Html;
use yii\data\ActiveDataProvider;
use yii\db\Expression;

use detalika\requests\OuterRoutes;
use detalika\requests\common\CommonUrls;
use detalika\requests\common\CurrentUser;
use detalika\requests\common\AccessCheck;
use detalika\requests\models\Request;
use detalika\requests\models\RequestPosition;
use detalika\requests\models\RequestMessage;
use detalika\requests\models\RequestPositionStatus;
use detalika\requests\models\outer\Good;
use detalika\requests\models\outer\DeliveryPartner;
use detalika\requests\models\outer\Article;
use detalika\requests\helpers\DateTimeConstsInterface;
use detalika\requests\helpers\StandartAttributesTrait;
use detalika\picking\widgets\RequestPositionPickingButton;

class RequestPositionSearch extends RequestPosition implements DateTimeConstsInterface
{
    use StandartAttributesTrait;
    
    const DATES_SEPARATOR = ' - ';
    
    public $term;
    public $requestMessagesCountFilter;
    public $pickingRequestPositionUser;
    
    // Для извлечения данных необходимых для построении иерархии.
    public $level;
    public $pathstr;
    
    public function rules() 
    {
        return [  
            [
                [
                    'id', 
                    'requests_request_id',
                    'requests_request_position_status_id',
                    'parent_id',
                    'goods_good_id',
                    'price', 
                    'quantity',
                    'delivery_partner_id',
                    'goods_article_id',
                    'requestMessagesCountFilter'
                ], 
                'integer'
            ],
            [
                [
                    'name', 
                    'good_name',
                    'link_to_search',
                ], 
                'safe'
            ],
            ['visible', 'boolean'],
            [['created', 'updated'], 'safe'],
            ['term', 'safe'],
        ];
    }
    
    public function formName() 
    {
        return '';
    }
    
    public function getDataProvider()
    {        
        $treeSubQuerySql = new Expression(<<<SQL
(
    WITH RECURSIVE tree (id, level, pathstr) AS 
    (
        SELECT id, 0, id::text as nodeName
            FROM requests_request_positions
            WHERE parent_id IS NULL
        UNION ALL
        SELECT  requests_request_positions.id, level + 1, pathStr || '.' || requests_request_positions.id::text 
            FROM requests_request_positions
            INNER JOIN tree ON tree.id = requests_request_positions.parent_id
    )
    SELECT id, level, pathstr FROM tree
)
SQL
);
        $treeSubQuery = ['rpTree' => $treeSubQuerySql];
        
        $requestPositionTable = self::tableName();
        $requestMessageTable = RequestMessage::tableName();
        $goodTable = Good::tableName();
        $deliveryPartnerTable = DeliveryPartner::tableName();
        $articleTable = Article::tableName();
        
        $goodNameOuterFieldName = Good::getFieldName('name');
        $deliveryPartnerNameField = DeliveryPartner::getFieldName('name');
        $articleNameField = Article::getFieldName('name');
        
        $goodNameOuterFieldNameFull = $goodTable . '.' . $goodNameOuterFieldName;
        $deliveryPartnerNameFieldFull = $deliveryPartnerTable . '.' . $deliveryPartnerNameField;
        $articleNameFieldFull = $articleTable . '.' .$articleNameField;

        $q = self::find()
            ->select([
                $requestPositionTable . '.*',
                "$goodNameOuterFieldNameFull AS goodNameOuter",
                "$deliveryPartnerNameFieldFull AS deliveryPartnerName",
                "$articleNameFieldFull AS articleName",
                "COUNT($requestMessageTable.*) AS requestMessagesCount",    
                "rpTree.level AS level",
                "rpTree.pathstr AS pathstr",
            ])
            ->joinWith([
                'good', 
                'article', 
                'deliveryPartner', 
                'requestMessages',
                'requestPositionUsers'
            ])
            ->leftJoin($treeSubQuery, $requestPositionTable.'.id = "rpTree".id')
            ->with(['requestPositionStatus']);

       $q->addOrderBy('"rpTree".pathstr');

        $dataProvider = new ActiveDataProvider([
            'query' => $q,
            'sort' => [
                'attributes' => [
                    'id',
                    'requests_request_id',
                    'requests_request_position_status_id',
                    'parent_id',
                    'name',
                    'good_name',
                    'price',
                    'quantity',
                    'visible',
                    'created',
                    'updated',
                    'link_to_search',
                    'goods_good_id' => [
                        'asc' => [$goodNameOuterFieldNameFull => SORT_ASC],
                        'desc' => [$goodNameOuterFieldNameFull => SORT_DESC],        
                    ],
                    'delivery_partner_id' => [
                        'asc' => [$deliveryPartnerNameFieldFull => SORT_ASC],
                        'desc' => [$deliveryPartnerNameFieldFull => SORT_DESC],        
                    ],
                    'goods_article_id' => [
                        'asc' => [$articleNameFieldFull => SORT_ASC],
                        'desc' => [$articleNameFieldFull => SORT_DESC],
                    ],
                    'requestMessagesCountFilter' => [
                        'asc' => ['requestMessagesCount' => SORT_ASC],
                        'desc' => ['requestMessagesCount' => SORT_DESC],
                    ], 
                ],
            ],
        ]);
        
        $q->andFilterWhere([$requestPositionTable . '.id' => $this->id])
            ->andFilterWhere([$requestPositionTable . '.requests_request_id' => $this->requests_request_id])
            ->andFilterWhere([$requestPositionTable . '.requests_request_position_status_id' => $this->requests_request_position_status_id])
            ->andFilterWhere([$requestPositionTable . '.parent_id' => $this->parent_id])
            ->andFilterWhere([$requestPositionTable . '.goods_good_id' => $this->goods_good_id])
            ->andFilterWhere([$requestPositionTable . '.price' => $this->price])
            ->andFilterWhere([$requestPositionTable . '.quantity' => $this->quantity])
            ->andFilterWhere([$requestPositionTable . '.delivery_partner_id' => $this->delivery_partner_id])
            ->andFilterWhere([$requestPositionTable . '.goods_article_id' => $this->goods_article_id])
            ->andFilterWhere([$requestPositionTable . '.visible' => $this->visible]);
        
        $q->andFilterWhere(['ILIKE', $requestPositionTable . '.name', $this->name])
            ->andFilterWhere(['ILIKE', $requestPositionTable . '.good_name', $this->good_name])
            ->andFilterWhere(['ILIKE', $requestPositionTable . '.link_to_search', $this->link_to_search]); 
    
        $q->andFilterHaving(["COUNT($requestMessageTable.*)" => $this->requestMessagesCountFilter]);
                
        
        //  Для поиска по term через ajax, когда результат 
        //  вовзращается, например, в Select2, который может быть 
        //  вообще в другом модуле.
        $q->andFilterWhere(['or', 
            ['ILIKE', $requestPositionTable . '.name', $this->term],
            ['ILIKE', $requestPositionTable. '.good_name', $this->term],
            ['ILIKE', $goodNameOuterFieldNameFull, $this->term],
            ['ILIKE', $articleNameFieldFull, $this->term],
        ]);
        
        $this->addDateIntervalConditionsToStandartAttributes(
            $q, 
            self::DATE_FORMAT, 
            self::DATES_SEPARATOR
        );
        
        $q->groupBy([
            $requestPositionTable.'.id',
            $goodNameOuterFieldNameFull,
            $deliveryPartnerNameFieldFull,
            $articleNameFieldFull,
            "rpTree.level",
            "rpTree.pathstr",
        ]); 
        
        $this->addOptionalConditionsToQuery($q);

        return $dataProvider;
    }
    
    // Будет использоваться для переопределения в наследниках.
    protected function addOptionalConditionsToQuery($query)
    {
        
    }
    
    public function search()
    {
        return $this->getDataProvider();
    }
    
    public function getGridColumns() 
    {        
        $canCurrentUserPicking = AccessCheck::instance()->canCurrentUserPicking();
        
        $requestsUrl = CommonUrls::getRequestsUrlForAjaxList();
         // Родителем для фильтра, разумеется, может быть любая позиция.
        $parentIdUrl = CommonUrls::getRequestPositionsUrlForAjaxList();
        $requestPositionStatusUrl = CommonUrls::getRequestPositionStatuesUrlForAjaxList();
        $detailsArticleUrl = Url::to(OuterRoutes::getRoute('detailsArticles'));
        $goodsUrl = Url::to(OuterRoutes::getRoute('goods'));
        $deliveryPartnersUrl = Url::to(OuterRoutes::getRoute('deliveryPartners'));
       
        $goodsIdField = Good::getFieldName('id');
        $goodsNameField = Good::getFieldName('name');
        $articlesIdField = Article::getFieldName('id');
        $articlesNameField = Article::getFieldName('name');
        $deliveryPartnersIdField = DeliveryPartner::getFieldName('id');
        $deliveryPartnerNameField = DeliveryPartner::getFieldName('name');
                
        $requestIdColumn =  $this->getSelect2AjaxColumn(
            'requests_request_id', 
            $requestsUrl,
            Request::className(),
            'id',
            'id'
        );
        
        $goodIdColumn = $this->getSelect2AjaxColumn(
            'goods_good_id', 
            $goodsUrl,
            Good::className(),
            $goodsIdField,
            $goodsNameField,
            'goodNameOuter'
        );
        
        $articleColumn = $this->getSelect2AjaxColumn(
            'goods_article_id', 
            $detailsArticleUrl,
            Article::className(),
            $articlesIdField,
            $articlesNameField,
            'articleName'
        );
        
        $deliveryPartnerColumn = $this->getSelect2AjaxColumn(
            'delivery_partner_id',
            $deliveryPartnersUrl,
            DeliveryPartner::className(), 
            $deliveryPartnersIdField,
            $deliveryPartnerNameField,
            'deliveryPartnerName'
        );
        
        $parentIdColumn = $this->getSelect2AjaxColumn(
            'parent_id',
            $parentIdUrl,
            RequestPosition::className(),
            'id',
            'positionName',
            'parentPositionName'
        );
        
        $requestPositionStatusIdColumn = $this->getSelect2AjaxColumn(
            'requests_request_position_status_id',
            $requestPositionStatusUrl, 
            RequestPositionStatus::className(),
            'id',
            'name',
            'requestPositionStatusName'
        );
        
        $columns = [
            'id' => [
                'attribute' => 'id',
                /*
                // Чтобы показать вложенность.
                'format' => 'raw',
                'value' => function($model, $key, $index) {
                    $result = '';
                    for ($i = 0; $i < $model->level; $i++) {
                        $result .= '&nbsp;';
                    }
                    $result .= $model->id;

                    return $result;
                },   
                */
            ],
            'requests_request_id' => $requestIdColumn,
            'name' => [
                'attribute' => 'name',
            ],
            'goods_good_id' => $goodIdColumn,
            'good_name' =>[
                'attribute' => 'good_name',
            ],
            'price' => [
                'attribute' => 'price',
            ],
            'quantity' => [
                'attribute' => 'quantity',
            ],
            'delivery_partner_id' => $deliveryPartnerColumn,
            'goods_article_id' => $articleColumn,    
            'link_to_search' => [
                'attribute' => 'link_to_search',
                'format' => 'raw',
                'value' => function($model, $key, $index, $column) {
                    $url = $model->link_to_search;
                    if (empty($url)) {
                        return null;
                    }
                    return Html::a($url, $url, ['target' => '_blank']);
                },
            ],
            'parent_id' => $parentIdColumn,
            'requestMessagesCountFilter' => [
                'label' => 'Количество сообщений',
                'attribute' => 'requestMessagesCountFilter',
                'value' => function($model, $key, $index, $column) {
                    if ($model->requestMessages === null) {
                        return 0;
                    }
                    
                    return count($model->requestMessages);
                },
            ], 
            'requests_request_position_status_id' => $requestPositionStatusIdColumn,
            'visible' => $this->getVisibleColumn(),
            'created' => $this->getCreatedColumn(self::DATE_FORMAT, self::DATE_TIME_FORMAT_YII, self::DATES_SEPARATOR),
            'updated' => $this->getUpdatedColumn(self::DATE_FORMAT, self::DATE_TIME_FORMAT_YII, self::DATES_SEPARATOR),
            
            'pickingRequestsPositionUser' => [], // Для сохранения нужного порядка.
                        
            'actions' => $this->getActionColumn(),
        ];
                
        if ($canCurrentUserPicking) {
            $columns['pickingRequestsPositionUser'] = [
                'attribute' => 'pickingRequestsPositionUser',
                'label' => 'Подбор',
                'format' => 'raw',
                'value' => function($model, $key, $index, $column) { 
                    $isPickingOn = CurrentUser::instance()->isPickingRequestPosition($model);
                    
                    return RequestPositionPickingButton::widget([
                        'isPickingOn' => $isPickingOn,
                        'requestPositionId' => $model->id,
                    ]);
                },
            ];
        } else {
            unset ($columns['pickingRequestsPositionUser']);
        }
        
        return $columns;
    } 
}