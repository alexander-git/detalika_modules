<?php
namespace detalika\requests\models\search;

use yii\data\ActiveDataProvider;

use detalika\requests\models\RequestPosition;
use detalika\requests\models\outer\Good;
use detalika\requests\models\outer\Article;

class RequestPositionParentSearch extends RequestPosition 
{    
    public $term;
    
    public function rules() 
    {
        return [  
            [['id', 'requests_request_id'],  'integer'],
            ['term', 'safe'],
        ];
    }
    
    public function formName() 
    {
        return '';
    }
    
    public function getDataProvider()
    {
        $requestPositionTable = self::tableName();

        $goodTable = Good::tableName();
        $articleTable = Article::tableName();
        
        $goodNameOuterFieldName = Good::getFieldName('name');
        $articleNameField = Article::getFieldName('name');
        $goodNameOuterFieldNameFull = $goodTable . '.' . $goodNameOuterFieldName;
        $articleNameFieldFull = $articleTable . '.' .$articleNameField;
                
        $q = self::find()
            ->joinWith(['good', 'article']);
        
        $dataProvider = new ActiveDataProvider([
            'query' => $q,
        ]);

        $q->andFilterWhere([$requestPositionTable . '.requests_request_id' => $this->requests_request_id]);
   
        if (!empty($this->id)) {
            $allChildsIds = RequestPosition::getAllChildIds($this->id);
            $q->andFilterWhere(['not in', $requestPositionTable . '.id', $allChildsIds]);
            
            // Позиция не может быть родителем сама себе.
            $q->andFilterWhere(['<>', $requestPositionTable . '.id', $this->id]); 
        }
        
        //  Для поиска по term через ajax.
        $q->andFilterWhere(['or', 
            ['ILIKE', $requestPositionTable . '.name', $this->term],
            ['ILIKE', $requestPositionTable. '.good_name', $this->term],
            ['ILIKE', $goodNameOuterFieldNameFull, $this->term],
            ['ILIKE', $articleNameFieldFull, $this->term],
        ]);
     
        return $dataProvider;
    }
     
    public function search()
    {
        return $this->getDataProvider();
    }
}