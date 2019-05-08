<?php
namespace detalika\auth\models\crud;

use yii\data\ActiveDataProvider;

class UserSearch extends User 
{
    public $term;
    
    public function rules() 
    {
        return [            
            ['id', 'integer'],
            ['email', 'safe'],
            ['term', 'safe'],
        ];
    }
    
    public function formName() 
    {
        return '';
    }

    public function getDataProvider()
    {
        $q = self::find();
        
        $dataProvider = new ActiveDataProvider([
            'query' => $q,
        ]);
        
        $q->andFilterWhere(['id' => $this->id])
            ->andFilterWhere(['ILIKE', 'email', $this->email])
            ->andFilterWhere(['ILIKE', 'email', $this->term]);
            
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
            'email',
        ];
    }   
}