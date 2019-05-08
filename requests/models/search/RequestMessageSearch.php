<?php

namespace detalika\requests\models\search;

use yii\helpers\Url;
use yii\data\ActiveDataProvider;

use detalika\requests\OuterRoutes;
use detalika\requests\models\RequestMessage;
use detalika\requests\models\outer\User;
use detalika\requests\helpers\DateTimeConstsInterface;
use detalika\requests\helpers\StandartAttributesTrait;

class RequestMessageSearch extends RequestMessage implements DateTimeConstsInterface
{
    use StandartAttributesTrait;
    
    const DATES_SEPARATOR = ' - ';
    
    public function rules() 
    {
        return [  
            [
                [
                    'id', 
                    'requests_request_id',
                    'requests_request_position_id',
                    'user_id',
                ], 
                'integer'
            ],
            [['text'], 'safe'],
            ['visible', 'boolean'],
            [['created', 'updated'], 'safe'],
        ];
    }
    
    public function formName() 
    {
        return '';
    }

    public function getDataProvider()
    {        
        $requestMessageTable = self::tableName();
        $userTable = User::tableName();
 
        $userLoginFieldName = User::getFieldName('login');
        $userEmailFieldName = User::getFieldName('email');
        $userLoginFieldNameFull = $userTable . '.' . $userLoginFieldName;
        $userEmailFieldNameFull = $userTable . '.' . $userEmailFieldName;
        
        $q = self::find()
            ->joinWith(['user']);

        $dataProvider = new ActiveDataProvider([
            'query' => $q,
            'sort' => [
                'attributes' => [
                    'id',
                    'requests_request_id',
                    'requests_request_position_id',
                    'text',
                    'visible',
                    'created',
                    'updated',
                    'user_id' => [
                        'asc' => [
                            $userEmailFieldNameFull => SORT_ASC,
                            $userLoginFieldNameFull => SORT_ASC,
       
                        ],
                        'desc' => [
                            $userEmailFieldNameFull => SORT_DESC,
                            $userLoginFieldNameFull => SORT_DESC,
                        ],
                    ],
                ],
            ],
        ]);

        $q->andFilterWhere([$requestMessageTable . '.id' => $this->id])
            ->andFilterWhere([$requestMessageTable . '.requests_request_id' => $this->requests_request_id])
            ->andFilterWhere([$requestMessageTable . '.requests_request_position_id' => $this->requests_request_position_id])
            ->andFilterWhere([$requestMessageTable . '.user_id' => $this->user_id])
            ->andFilterWhere([$requestMessageTable . '.visible' => $this->visible]);
        
        $q->andFilterWhere(['ILIKE', 'text', $this->text]);
        
        $this->addDateIntervalConditionsToStandartAttributes(
            $q, 
            self::DATE_FORMAT, 
            self::DATES_SEPARATOR
        );
        
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
        $usersUrl = Url::to(OuterRoutes::getRoute('users'));  
        
        $usersIdFieldName = User::getFieldName('id');
        $usersEmailFieldName = User::getFieldName('email');
        
        $userIdColumn = $this->getSelect2AjaxColumn(
            'user_id', 
            $usersUrl , 
            User::className(), 
            $usersIdFieldName , 
            $usersEmailFieldName, 
            'userLogin'
        );
    
        return [
            'id' => [
                'attribute' => 'id',
            ],
            'requests_request_id' => [
                'attribute' => 'requests_request_id',
            ],
            'requests_request_position_id' => [
                'attribute' => 'requests_request_position_id',
            ],
            'user_id' => $userIdColumn,
            'text' => [
                'attribute' => 'text',
            ],    
            'visible' => $this->getVisibleColumn(),
            'created' => $this->getCreatedColumn(self::DATE_FORMAT, self::DATE_TIME_FORMAT_YII, self::DATES_SEPARATOR),
            'updated' => $this->getUpdatedColumn(self::DATE_FORMAT, self::DATE_TIME_FORMAT_YII, self::DATES_SEPARATOR),
            'actions' => $this->getActionColumn(),
        ];
    } 
}