<?php

namespace detalika\requests\models\search;

use yii\data\ActiveDataProvider;

use detalika\requests\models\RequestStatus;
use detalika\requests\helpers\DateTimeConstsInterface;
use detalika\requests\helpers\StandartAttributesTrait;

class RequestStatusSearch extends RequestStatus implements DateTimeConstsInterface
{
    use StandartAttributesTrait;
    
    const DATES_SEPARATOR = ' - ';
    
    public function rules() 
    {
        return [            
            ['id', 'integer'],
            [['name', 'type'], 'safe'],
            ['visible', 'boolean'],
            [['created', 'updated'], 'safe'],
        ];
    }

    public function getDataProvider()
    {
        $q = self::find();
        
        $dataProvider = new ActiveDataProvider([
            'query' => $q,
        ]);
        
        $q->andFilterWhere(['id' => $this->id])
            ->andFilterWhere(['visible' => $this->visible])
            ->andFilterWhere(['type' => $this->type])
            ->andFilterWhere(['ILIKE', 'name', $this->name]);
    
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
            'id',
            'name',
            $this->getSelectColumn('type', self::getTypesArray(), 'typeName'),
            $this->getVisibleColumn(),
            $this->getCreatedColumn(self::DATE_FORMAT, self::DATE_TIME_FORMAT_YII, self::DATES_SEPARATOR),
            $this->getUpdatedColumn(self::DATE_FORMAT, self::DATE_TIME_FORMAT_YII, self::DATES_SEPARATOR),
            $this->getActionColumn(),
        ];
    }   
}