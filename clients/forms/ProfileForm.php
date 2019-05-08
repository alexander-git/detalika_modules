<?php

namespace detalika\clients\forms;

use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\grid\GridView as YiiGridView;

use kartik\grid\GridView;
use kartik\grid\ActionColumn;
use execut\crudFields\fields\Id;
use execut\crudFields\fields\Boolean;
use execut\crudFields\Behavior;

use detalika\picking\widgets\ProfilePickingButton;

use detalika\clients\common\CurrentUser;
use detalika\clients\helpers\Select2Helper;
use detalika\clients\models\Profile;
use detalika\clients\models\Type;
use detalika\clients\models\Contact;
use detalika\clients\models\Card;
use detalika\clients\models\ContactType;
use detalika\clients\models\CardType;
use detalika\clients\models\Source;
use detalika\clients\models\Shop;
use detalika\clients\models\User;
use detalika\clients\OuterRoutes;

use detalika\clients\helpers\DateTimeConstsInterface;
use detalika\clients\helpers\ListsTrait;


class ProfileForm extends Profile implements DateTimeConstsInterface
{
    use ListsTrait;
        
    const DATES_SEPARATOR = ' - ';
    
    public $term;
    
    // Для работы фильтров.
    public $contactTypeId;
    public $contactValue;
    public $cardTypeId;
    public $cardName;
    public $pickingProfileUser;
    
    public function rules() 
    {
        return [
            [   
                [
                    'name', 
                    'surname',
                    'patronymic',
                    'company_name',
                    'city',
                    'delivery_address',
                    'comments',
                ],
                'safe'
            ],
            [['id', 'clients_type_id', 'user_id'], 'integer'],
            ['visible', 'boolean'],
            [['created', 'updated'], 'safe'],
            
            ['contactTypeId', 'integer'],
            ['contactValue', 'safe'],
            
            ['cardTypeId', 'integer'],
            ['cardName', 'safe'],
            
            ['clients_source_id', 'integer'],
            ['shop_id', 'integer'],
            ['accountId', 'safe'],
            
            ['term', 'safe'],
        ];
    }
    
    public function formName()
    {
        return '';
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
                'surname' => [
                    'attribute' => 'surname',
                ],
                'patronymic' => [
                    'attribute' => 'patronymic',
                ],
                'user_id' => [],
                'clients_type_id' => [],
                'company_name' => [
                    'attribute' => 'company_name',
                ],
                'city' => [
                    'attribute' => 'city',
                ],
                'delivery_address' => [
                    'attribute' => 'delivery_address',
                ], 
                'visible' => [
                    'class' => Boolean::className(),
                    'attribute' => 'visible',
                ],
                'created' => [],
                'updated' => [],             
                'contacts' => [],
                'cards' => [],
                'clients_source_id' => [],
                'shop_id' => [], 
                'pickingProfileUser' => [], 
                'actions' => [],
             ],
         ];
        
        return $behaviors;
    }

    
    public function getDataProvider()
    {
        
        $profileTable = self::tableName();
        $contactTable = Contact::tableName();
        $cardTable = Card::tableName();
        
        /*
        $pickingProfileUserTable = ProfileUser::tableName();
        
        $profileUsersProfileIdFieldName = ProfileUser::getFieldName('profile_id');
        $profileUsersUserIdFieldName = ProfileUser::getFieldName('user_id');
        $profileUsersUserIdFieldNameFull = $pickingProfileUserTable . '.' .$profileUsersUserIdFieldName; 
        */

        $q = self::find()
            ->joinWith([
                'contacts' => function($q) {
                    $q->with('contactType');
                },
                'cards' => function($q) {
                    $q->with('cardType');
                },
                'user',
                'profileUsers',
                'type',
            ])
            ->with(['source', 'shop', 'type']);
        
        
        
        $dataProvider = new ActiveDataProvider([
            'query' => $q,
        ]);
        
        $q->andFilterWhere([$profileTable . '.id' => $this->id])
            ->andFilterWhere([$profileTable . '.clients_type_id' => $this->clients_type_id])
            ->andFilterWhere([$profileTable. '.clients_source_id' => $this->clients_source_id])
            ->andFilterWhere([$profileTable . '.shop_id' => $this->shop_id]);
        
        if (!empty($this->user_id)) {
            if (is_array($this->user_id)) {
                $q->andFilterWher(['in', $profileTable . '.user_id', $this->user_id]);
            } else {
                $q->andFilterWhere([$profileTable . '.user_id' => $this->user_id]);  
            }
        }
                
        $q->andFilterWhere(['ILIKE', $profileTable . '.name', $this->name])
            ->andFilterWhere(['ILIKE', $profileTable . '.surname', $this->surname])
            ->andFilterWhere(['ILIKE', $profileTable . '.patronymic', $this->patronymic])
            ->andFilterWhere(['ILIKE', $profileTable . '.company_name', $this->company_name])
            ->andFilterWhere(['ILIKE', $profileTable . '.city', $this->city])
            ->andFilterWhere(['ILIKE', $profileTable . '.delivery_address', $this->delivery_address])
            ->andFilterWhere(['ILIKE', $profileTable . '.comments', $this->comments]);
            
        $q->andFilterWhere(['or', 
            ['ILIKE', $profileTable . '.name', $this->term],
            ['ILIKE', $profileTable . '.surname', $this->term],
            ['ILIKE', $profileTable . '.patronymic', $this->term],
        ]);
        
        $this->addDateIntervalConditionsToQuery($q);
        
        $q->andFilterWhere([$contactTable . '.clients_contacts_type_id' => $this->contactTypeId]);    
        $q->andFilterWhere(['ILIKE', $contactTable . '.value', $this->contactValue]);
        
        $q->andFilterWhere([$cardTable . '.clients_cards_type_id' => $this->cardTypeId]);
        $q->andFilterWhere(['ILIKE', $cardTable . '.name', $this->cardName]);
        
        return $dataProvider;
    }
    
    public function search()
    {
        return $this->getDataProvider();
    }
    
    public function getGridColumns()
    {
        $authUserUrl = Url::to(OuterRoutes::getRoute('authUsers'));

        $dateRangeFilterWidgetsOptions = [
            'model' => $this,
            'presetDropdown' => false,
            'convertFormat' => true,
            'pluginOptions'=> [                                          
                'locale' => [
                    'format' => self::DATE_FORMAT,
                    'separator' => self::DATES_SEPARATOR,
                ],
                'opens'=> 'left'
            ]
        ];
        
        if (!empty($this->user_id)) {
            $usersIdField = User::getFieldName('id');
            $usersEmailField = User::getFieldName('email');
            
            if (is_array($this->user_id)) {
                $users = User::find()
                    ->andWhere(['in', $usersIdField, $this->user_id])
                    ->all();
            } else {
                $users = User::find()
                    ->andWhere([$usersIdField => $this->user_id])
                    ->all();
            }
            
            $userIdInitText = ArrayHelper::map($users, $usersIdField, $usersEmailField);
        } else {
            $userIdInitText  = '';
        }
        
        $columns = $this->getBehavior('fields')->getGridColumns();
        
        $columns['user_id'] = [
            'attribute' => 'user_id',
            'format' => 'raw',
            'value' => function($model, $key, $index, $column) {
                return $model->userEmail;
            },
            'filterType' => GridView::FILTER_SELECT2,
            'filterWidgetOptions' => [
                'options' => [
                    'placeholder' => 'Введите email',
                ],
                'initValueText' => $userIdInitText,
                'pluginOptions' => [
                    'allowClear' => true,
                    'ajax' => [
                        'url' => $authUserUrl,
                        'dataType' => 'json',
                        'data' => Select2Helper::getStandartAjaxDataJs(),
                    ],
                ],
            ],
        ];
            
        $columns['clients_type_id'] = [
            'attribute' => 'clients_type_id',
            'value' => function($model, $key, $index, $colum) {
                return $model->clientTypeName;
            },
            'filter' => Type::getItemsList(),
        ];
                  
        $columns['created'] = [
            'attribute' => 'created',
            'format' => ['date', self::DATE_TIME_FORMAT_YII],
            'filterType' => GridView::FILTER_DATE_RANGE, 
            'filterWidgetOptions' => $dateRangeFilterWidgetsOptions,
        ];
        
        $columns['updated']  = [
            'attribute' => 'updated',
            'format' => ['date', self::DATE_TIME_FORMAT_YII],
            'filterType' => GridView::FILTER_DATE_RANGE, 
            'filterWidgetOptions' => $dateRangeFilterWidgetsOptions,
        ];
        
        $columns['contacts'] =  [
            'attribute' => 'contacts',
            'format' => 'raw',
            'value' => function($model, $key, $index, $column) { 
                return $this->getContactsHtml($model);
            },
            'filter' => $this->getContactsFilterHtml(),
        ];
        
        $columns['cards']  = [
            'attribute' => 'cards',
            'format' => 'raw',
            'value' => function($model, $key, $index, $column) { 
                return $this->getCardsHtml($model);
            },
            'filter' => $this->getCardsFilterHtml(),
        ];
                
        $columns['clients_source_id'] = [
            'attribute' => 'clients_source_id',
            'format' => 'raw',
            'value' => function($model, $key, $index, $column) { 
                return $model->sourceName;
            },
            'filterType' => GridView::FILTER_SELECT2,
            'filterWidgetOptions' => [
                'data' => Source::getItemsList(),
                'options' => [
                    'placeholder' => 'Выберете источник',
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ],
        ];
            
        $columns['pickingProfileUser'] = [
            'attribute' => 'pickingProfileUser',
            'label' => 'Подбор',
            'format' => 'raw',
            'value' => function($model, $key, $index, $column) { 
                $isPickingOn = CurrentUser::instance()->isPickingProfile($model);    
                
                return ProfilePickingButton::widget([
                    'isPickingOn' => $isPickingOn,
                    'clientProfileId' => $model->id,
                ]);
            },
        ];
            
        $columns['shop_id'] = [
           'attribute' => 'shop_id',
           'format' => 'raw',
           'value' => function($model, $key, $index, $column) { 
               return $model->shopName;
           },
           'filterType' => GridView::FILTER_SELECT2,
           'filterWidgetOptions' => [
               'data' => Shop::getItemsList(),
               'options' => [
                   'placeholder' => 'Выберете магазин',
               ],
               'pluginOptions' => [
                   'allowClear' => true
               ],
            ],
        ];
           
        $columns['actions'] = [
            'class' => ActionColumn::className(),
            'template' => '{update} {delete}',
        ];
        
        return $columns;
    }
    
    private function addDateIntervalConditionsToQuery($query)
    {
        $t = self::tableName(); 
        if (
            !empty($this->created) && 
            strpos($this->created, self::DATES_SEPARATOR) !== false
        ) {
            list($createdFrom, $createdTo) = explode(self::DATES_SEPARATOR, $this->created);
            
            $createdFrom = $createdFrom . ' 00:00:00';
            $createdTo = $createdTo . ' 23:59:59';
            
            $query->andFilterWhere(['>=', $t . '.created', $createdFrom])
                ->andFilterWhere(['<=', $t . '.created', $createdTo]); 
        }

        if (
            !empty($this->updated) && 
            strpos($this->updated, self::DATES_SEPARATOR) !== false
        ) {
            list($updatedFrom, $updatedTo) = explode(self::DATES_SEPARATOR, $this->updated);
            
            $updatedFrom = $updatedFrom . ' 00:00:00';
            $updatedTo = $updatedTo . ' 23:59:59';       

            $query->andFilterWhere(['>=', $t . '.updated', $updatedFrom])
                ->andFilterWhere(['<=', $t . '.updated', $updatedTo]); 
        }
    
        return $query;
    }
    
    private function getContactsHtml($profile) 
    {
        if (count($profile->contacts) === 0) {
            return '';
        }
        
        $dataProvider = new ArrayDataProvider([
            'allModels' => $profile->contacts,
        ]);
        
        $result = YiiGridView::widget([
            'dataProvider' => $dataProvider,
            'showOnEmpty' => false,
            'showHeader' => false,
            'summary' => '',
            'columns' => [
                'contactType.name',
                'value',
                [
                    'attribute' => 'is_main', 
                    'value' => function($model) {
                        if ($model->is_main) {
                            return 'Предпочтительный';
                        } else {
                            return '';
                        }
                    },
                ],
            ],
        ]);            
        return  $result;
    }
    
    private function getCardsHtml($profile) 
    {
        if (count($profile->cards) === 0) {
            return '';
        }
        
        $dataProvider = new ArrayDataProvider([
            'allModels' => $profile->cards,
        ]);
        
         $result = YiiGridView::widget([
            'dataProvider' => $dataProvider,
            'showOnEmpty' => false,
            'showHeader' => false,
            'summary' => '',
            'columns' => [
                'cardType.name',
                'name',
            ],
        ]);            
        return  $result;
    }
    
    private function getContactsFilterHtml()
    {
        $searchModel = $this;
        $commonOptions = [
            'class' => 'form-control', 
            'style' => 'width : 48%;'
        ];
        
        $selectOptions = ArrayHelper::merge(['prompt' => ''], $commonOptions);
        $selectHtml = Html::activeDropDownList(
            $searchModel, 
            'contactTypeId', 
            ContactType::getItemsList(),
            $selectOptions
        );
        $inputHtml = Html::activeTextInput($searchModel, 'contactValue', $commonOptions);
        
        $formContent = $selectHtml . "\n" . $inputHtml;
        return $this->getInlineFormHtml($formContent);
    }
    
   
    private function getCardsFilterHtml()
    {
        $searchModel = $this;
        $commonOptions = [
            'class' => 'form-control', 
            'style' => 'width : 48%;'
        ];
        
        $selectOptions = ArrayHelper::merge(['prompt' => ''], $commonOptions);
        $selectHtml = Html::activeDropDownList(
            $searchModel, 
            'cardTypeId', 
            CardType::getItemsList(),
            $selectOptions
        );
        $inputHtml = Html::activeTextInput($searchModel, 'cardName', $commonOptions);
        
        $formContent = $selectHtml . "\n" . $inputHtml;
        return $this->getInlineFormHtml($formContent);
    }
         
    private function getInlineFormHtml($formContent)
    {
        return Html::tag('form', $formContent, ['class' => 'form-inline']);
    }
}