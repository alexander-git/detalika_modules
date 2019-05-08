<?php

namespace detalika\clients\forms;

use yii\data\ActiveDataProvider;

use kartik\grid\ActionColumn;

use execut\crudFields\Behavior;
use execut\crudFields\fields\Id;

use detalika\clients\models\CardType;

class CardTypeForm extends CardType
{
    public function rules() 
    {
        return [            
            ['id', 'integer'],
            ['name', 'safe'],
        ];
    }
    
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['fields'] = [
             'class' => Behavior::className(),
             'fields' => [
                'id' => [
                    'class' => Id::className(),
                    'attribute' => 'id',
                ],
                'name' => [
                    'attribute' => 'name',
                ],
                'actions' => [],
             ],
         ];
        
        return $behaviors;
    }

    public function getDataProvider()
    {
        $q = self::find();
        
        $dataProvider = new ActiveDataProvider([
            'query' => $q,
        ]);
        
        $q->andFilterWhere(['id' => $this->id])
            ->andFilterWhere(['ILIKE', 'name', $this->name]);
            
        return $dataProvider;
    }
    
    public function search()
    {
        return $this->getDataProvider();
    }
    

    public function getGridColumns() 
    {     
        $columns = $this->getBehavior('fields')->getGridColumns();
        
        $columns['actions'] = [
            'class' => ActionColumn::className(),
            'template' => '{update} {delete}',
        ];
        
        return $columns;
    } 
    
}