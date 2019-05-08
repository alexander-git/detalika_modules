<?php

namespace detalika\delivery\models\search;

use yii\data\ActiveDataProvider;

use detalika\delivery\models\Stock;
use detalika\delivery\helpers\DateTimeConstsInterface;
use detalika\delivery\helpers\StandartAttributesTrait;

class StockSearch extends Stock implements DateTimeConstsInterface
{
    use StandartAttributesTrait;
        
    public $term;

    public function rules() 
    {
        return [  
            ['term', 'safe'],
            ['id', 'integer'],
            [
                [
                    'name', 
                    'address',
                    'work_time',
                    'ext_uuid'
                ], 
                'safe'
            ],
            ['visible', 'boolean'],
            [['created', 'updated'], 'safe'],
        ];
    }
    
    public function formName() 
    {
        // Чтобы ajax-запросом(например, из другого модуля) можно было
        // просто отправлять в GET параметр term, а не Stock[term] и
        // внешенему коду не будет нужна была информация о точнои названии
        // модели.
        return '';
    }

    public function getDataProvider()
    {        
        $stockTable = self::tableName();
        
        $q = self::find();
        
        $dataProvider = new ActiveDataProvider([
            'query' => $q,
        ]);

        $q->andFilterWhere([$stockTable . '.id' => $this->id]);
           
        $q->andFilterWhere(['ILIKE', $stockTable . '.name', $this->name])
            ->andFilterWhere(['ILIKE', $stockTable . '.address', $this->address])
            ->andFilterWhere(['ILIKE', $stockTable . '.work_time', $this->work_time])
            ->andFilterWhere(['ILIKE', $stockTable . '.ext_uuid', $this->ext_uuid]);
                    
        
        //  Для поиска по term через ajax, когда результат 
        //  вовзращается, например, в Select2, которыей может быть 
        //  вообще в другом модуле.
        $q->andFilterWhere(['ILIKE', $stockTable . '.name', $this->term]);

        $this->addDateIntervalConditionsToStandartAttributes(
            $q, 
            self::DATE_FORMAT, 
            self::DATES_SEPARATOR
        );

        return $dataProvider;
    }
    

    public function search()
    {
        return $this->getDataProvider();
    }
    
    public function getGridColumns() 
    {   
        return [
            'name' => [
                'attribute' => 'name',
            ],
            'address' => [
                'attribute' => 'address', 
            ],
            'work_time' => [
                'attribute' => 'work_time',            
            ],
            'ext_uuid' => [
                'attribute' => 'ext_uuid',
            ],
            'visible' => $this->getVisibleColumn(),
            'created' => $this->getCreatedColumn(self::DATE_FORMAT, self::DATE_TIME_FORMAT_YII, self::DATES_SEPARATOR),
            'updated' => $this->getUpdatedColumn(self::DATE_FORMAT, self::DATE_TIME_FORMAT_YII, self::DATES_SEPARATOR),
            'actions' => $this->getActionColumn(),
        ];
    } 
}