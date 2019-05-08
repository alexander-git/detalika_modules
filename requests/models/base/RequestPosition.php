<?php

namespace detalika\requests\models\base;

use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

use detalika\requests\models\outer\Good;
use detalika\requests\models\outer\Article;
use detalika\requests\models\outer\DeliveryPartner;
use detalika\requests\models\outer\RequestPositionUser;

/**
 * This is the model class for table "requests_request_positions".
 *
 * @property integer $id
 * @property string $created
 * @property string $updated
 * @property boolean $visible
 * @property string $name
 * @property string $good_name
 * @property string $price
 * @property string $quantity
 * @property string $link_to_search
 * @property string $parent_id
 * @property string $requests_request_id
 * @property string $requests_request_position_status_id
 * @property string $goods_article_id
 * @property string $goods_good_id
 * @property string $delivery_partner_id
 *
 * @property RequestMessage[] $requestsRequestMessages
 * @property DeliveryPartner $deliveryPartner
 * @property Article $article
 * @property Good $goodsGood
 * @property RequestPositionStatus $requestPositionStatus
 * @property Request $request
 */
class RequestPosition extends \yii\db\ActiveRecord
{
    const SCENARIO_VALIDATE_COPIED_FROM_GOOD_ATTRIBUTES = 'validateCopiedFromGoodAttributes';
    const SCENARIO_VALIDATE_INDIVIDUAL_IN_MASS_CREATE = 'validateIndividualInMassCreate';
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'requests_request_positions';
    }
    
    public function behaviors() 
    {
        $behaviors = parent::behaviors();
        $behaviors['timestamp'] = [
            'class' => TimestampBehavior::className(),
            'createdAtAttribute' => 'created',
            'updatedAtAttribute' => 'updated',
            'value' => (new Expression('NOW()')),
        ];
        
        return $behaviors;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['visible', 'boolean'],
            ['visible', 'default', 'value' => true],
        
            ['name', 'validateRequiredNameOrGoodOrArticleOrLinkToSearch', 'skipOnEmpty' => false],
            ['name', 'string', 'max' => 255],
            ['name', 'default', 'value' => null],
            
            ['goods_article_id', 'integer'],
            
            ['goods_good_id', 'integer'],

            ['good_name', 'string', 'max' => 255],
            ['good_name', 'default', 'value' => null],
            
            ['price', 'integer', 'min' => 0],
            ['price', 'default', 'value' => null],
                    
            ['quantity', 'integer', 'min' => 0],
            ['quantity', 'default', 'value' => null],
            
            ['delivery_partner_id', 'integer', 'min' => 0],
            ['delivery_partner_id', 'default', 'value' => null],
            
            // Использование on и except у requests_request_id связано 
            // с особенностями работы SaveWithRelationsBahavior 
            // в модели ClientCar.
            ['requests_request_id', 'safe', 'on' => [self::SCENARIO_DEFAULT]],
            ['requests_request_id', 'required', 'except' => [self::SCENARIO_DEFAULT]],
            ['requests_request_id', 'integer',  'except' => [self::SCENARIO_DEFAULT]],
            [
                'requests_request_id', 
                'exist', 
                'skipOnError' => true, 
                'targetClass' => Request::className(), 
                'targetAttribute' => ['requests_request_id' => 'id'],
                'except' => [self::SCENARIO_DEFAULT]
            ],
            
            ['link_to_search', 'url'],
            ['link_to_search', 'string', 'max' => 255],
            ['link_to_search', 'default', 'value' => null],
            
            ['requests_request_position_status_id', 'integer'],
            [
                'requests_request_position_status_id', 
                'exist', 
                'skipOnError' => true, 
                'targetClass' => RequestPositionStatus::className(), 
                'targetAttribute' => ['requests_request_position_status_id' => 'id'],
            ],
            [
                'requests_request_position_status_id',  
                'default', 
                'value' => RequestPositionStatus::getNewRequestPositionStatusId(),
            ],
            
            
            ['parent_id', 'integer'],
            [
                'parent_id',
                'exist',
                'skipOnError' => true,
                'targetClass' => RequestPosition::className(),
                'targetAttribute' => ['parent_id' => 'id'],
            ],
            ['parent_id', 'validateParentIdRequestOwner', 'skipOnEmpty' => true],
            ['parent_id', 'validateParentIdChildren', 'skipOnEmpty' => true],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'created' => 'Создан',
            'updated' => 'Обновлён',
            'visible' => 'Видимость',
            'name' => 'Название',
            'goods_good_id' => 'Товар',
            'good_name' => 'Название товара',
            'price' => 'Цена',
            'quantity' => 'Количество',
            'delivery_partner_id' => 'Поставщик',
            'goods_article_id' => 'Артикул',
            'link_to_search' => 'Ссылка на поиск',
            'requests_request_id' => 'Запрос',
            'requests_request_position_status_id' => 'Статус',
            'parent_id' => 'Родитель',     
            'requestMessagesCount' => 'Количество сообщений',
            'articleName' => 'Артикул',
            'positionName' => 'Название',
        ];
    }

    public function scenarios() 
    {
        $scenarios = parent::scenarios();
        
        // Валидацию этих атрибутов будем выполнять отдельно.
        $scenarios[self::SCENARIO_VALIDATE_COPIED_FROM_GOOD_ATTRIBUTES] = [
            '!good_name',
            '!pirce',
            '!quantity',
            '!delivery_partner_id',
        ];
        
                
        $scenarios[self::SCENARIO_VALIDATE_INDIVIDUAL_IN_MASS_CREATE] = [
            'name',
            'goods_article_id',
            'goods_good_id',
            'link_to_search',
        ];
        
        return $scenarios;
    }
    
    public function transactions() 
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }
    
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        
        if (!$this->updateGoodAttributesIdNeed($insert)) {
            return false;
        }
     
         // Если это вставка то дочерних элементов ещё нет.
        if (!$insert) {
            if (!$this->updateChildPositionsAndMessagesIfNeed()) {
                return false;
            }
        }
        
        
        return true;
    }
    
    public function beforeDelete()
    {
        if (!$this->updateChildPositionAndMessagesWhenDelete()) {
            return false;
        }
        
        return parent::beforeDelete();
    }
            
    public function getPositionName()
    {
        if (!empty($this->good_name) && !empty($this->price)) {
           return $this->good_name . ', '. $this->price;
        }
        
        if (!empty($this->article_name)) {
            return $this->article_name;
        }
        
        if (!empty($this->name)) {
            return $this->name;
        }
        
        if (!empty($this->link_to_search)) {
            return $this->link_to_search; 
        }
        
        return $this->link_to_search;
    }
                     
    public function getGoodNameOuter()
    {
        if ($this->good === null) {
            return null;
        }
        
        $nameField = Good::getFieldName('name');
        return $this->good->$nameField;
    }
    
    public function getDeliveryPartnerName()
    {
        if ($this->deliveryPartner === null) {
            return null;
        }
        
        $nameField = DeliveryPartner::getFieldName('name');
        return $this->deliveryPartner->$nameField;
    }
    
    public function getArticleName()
    {
        if ($this->article === null) {
            return null;
        }
        
        return $this->article->articleName;
    }
    
    public function getRequestPositionStatusName()
    {
        if (empty($this->requestPositionStatus)) {
            return null;
        }
        
        return $this->requestPositionStatus->name;
    }
    
    public function getParentPositionName()
    {
        if (empty($this->parent)) {
            return null;
        }
        
        return $this->parent->positionName;
    }
    
    public function getRequestMessagesCount()
    {
        return $this->getRequestMessages()->count();
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGood()
    {
        $idFieldName = Good::getFieldName('id');
        return $this->hasOne(Good::className(), [$idFieldName => 'goods_good_id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDeliveryPartner()
    {
        $idFieldName = DeliveryPartner::getFieldName('id');
        return $this->hasOne(DeliveryPartner::className(), [$idFieldName => 'delivery_partner_id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArticle()
    {
        $articleIdFieldName = Article::getFieldName('id');
        return $this->hasOne(Article::className(), [$articleIdFieldName => 'goods_article_id']);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRequestPositionStatus()
    {
        return $this->hasOne(RequestPositionStatus::className(), ['id' => 'requests_request_position_status_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRequest()
    {
        return $this->hasOne(Request::className(), ['id' => 'requests_request_id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRequestMessages()
    {
        return $this->hasMany(RequestMessage::className(), ['requests_request_position_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(self::className(), ['id' => 'parent_id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChildren()
    {
        return $this->hasMany(self::className(), ['parent_id' => 'id']);
    }
    
    public function getRequestPositionUsers()
    {
        $requestPositionUsersRequestPositionIdFieldName = 
            RequestPositionUser::getFieldName('request_position_id');
        return $this->hasMany(RequestPositionUser::className(), 
            [$requestPositionUsersRequestPositionIdFieldName => 'id']
        );
    }
    
    public function validateRequiredNameOrGoodOrArticleOrLinkToSearch($attribute, $params, $validator)
    {
        $errorMessage = 'Нужно указать или название или артикул или товар или ссылку на поиск.';
        if (
            empty($this->name) && 
            empty($this->goods_article_id) &&
            empty($this->goods_good_id) && 
            empty($this->link_to_search)
        ) {
            $this->addError('name', $errorMessage);
            $this->addError('goods_article_id', $errorMessage);
            $this->addError('goods_good_id', $errorMessage);
            $this->addError('link_to_search', $errorMessage);
        }
    }  
    
    public function validateParentIdRequestOwner($attribute, $params, $validator)
    {
        if (empty($this->request_id)) {
            return;
        }
        
        if (empty($this->parent_id)) {
            return;
        }
        
        if ((int) $this->parent->request_id !== (int) $this->request_id) {
            $errorMessage = 'Родитель должен относиться к тому же запросу, что и текущая позиция';
            $this->addError($attribute, $errorMessage);
        }
    }
    
    // Проверим, чтобы мы не установили родителем данной позиции один из её 
    // уже существующих потомков. 
    public function validateParentIdChildren($attribute, $params, $validator)
    {
        if (empty($this->parent_id)) {
            return;
        }
        
        if (empty($this->id)) {
            // Позиция запроса новая у неё ещё не может быть детей. 
            return;
        }
        
        if ((int) $this->id === (int) $this->parent_id) {
            $this->addError('parent_id', 'Родителем не может быть сама позиция.');  
        }
        
        $allChildIds = self::getAllChildIds($this->id);
        
        if (in_array((int) $this->parent_id, $allChildIds)) {
            $this->addError('parent_id', 'Родителем не может быть потомок текущей позиции.');
        }
    }
    
    public static function getAllChildIds($requestPositionId)
    {
        $allChildIds = [];
        $currentLevelParentIds = [$requestPositionId];
        do {
            $currentLevelChildIds = self::find()
                ->select(['id'])
                ->where(['in', 'parent_id', $currentLevelParentIds])
                ->column();
            
            foreach ($currentLevelChildIds as $childId) {
                $allChildIds []= (int) $childId;    
            }
            
            $currentLevelParentIds = $currentLevelChildIds;
        } while (count($currentLevelParentIds) > 0);
        
        return $allChildIds;
    }
    
    private function updateGoodAttributesIdNeed($insert)
    {
        // Будем копировать значения из товара если товар задан, либо если
        // id товара(goods_good_id) поменялось.
        $needCopyGoodRelatedAttributes = false;
        // Будем удалять значения связанные с товаром если
        // id товара(goods_good_id) обнуляется.
        $needRemoveGoodRelatedAttributes = false;
        $newGoodId = $this->goods_good_id;
        // Вставка записи.
        if ($insert) {
            if (!empty($newGoodId)) {
                $needCopyGoodRelatedAttributes = true;
            }
        } 
        
        // Обновление записи.
        if (!$insert) {
            $oldGoodId =  static::findOne(['id' => $this->id])->goods_good_id;    
            if (empty($newGoodId)) {
                $needRemoveGoodRelatedAttributes = true;
            }
            if (empty($oldGoodId) && !empty($newGoodId)) {
                $needCopyGoodRelatedAttributes = true;
            }
            if (
                !empty($oldGoodId) && 
                !empty($newGoodId) &&
                (int) $oldGoodId !== (int) $newGoodId
            ) {
                $needCopyGoodRelatedAttributes = true;
            }
        }
        
        if ($needCopyGoodRelatedAttributes) {
            $good = Good::findOne($this->goods_good_id);
            $goodsNameField = Good::getFieldName('name');
            $goodsPriceField = Good::getFieldName('price');
            $goodsCountField = Good::getFieldName('count');
            $goodsDeliveryPartnerIdField = Good::getFieldName('delivery_partner_id');
            
            // Скопируем нужные значения из товара.
            $this->good_name = $good->$goodsNameField;
            $this->price = $good->$goodsPriceField;
            $this->quantity = $good->$goodsCountField;
            $this->delivery_partner_id = $good->$goodsDeliveryPartnerIdField;
            
            $previousScenario = $this->scenario;
            $this->scenario = self::SCENARIO_VALIDATE_COPIED_FROM_GOOD_ATTRIBUTES;
            if (!$this->validate()) {
                return false;
            }
            
            $this->scenario = $previousScenario;
        }
        
        if ($needRemoveGoodRelatedAttributes) {
            $this->good_name = null;
            $this->price = null;
            $this->quantity = null;
            $this->delivery_partner_id = null;
        }
        
        return true;
    }
    
    private function updateChildPositionsAndMessagesIfNeed()
    {
        $newRequestId = (int) $this->requests_request_id;
        $oldRequestId = (int) self::find()
            ->select(['requests_request_id'])
            ->where(['id' => $this->id])
            ->scalar();
                
        if ($newRequestId === $oldRequestId) {
            // Запрос к которому относится позиция не меняется - ничего 
            // обновлять не надо.
            return true;
        }
        
        // Обновим все сообщения.
        $requestMessages = RequestMessage::find()
            ->where(['requests_request_position_id' => $this->id])
            ->all();
        
        foreach ($requestMessages as $requestMessage) {
            $requestMessage->requests_request_id = $newRequestId;
            // Валидацию отключим, так как там проверяется  
            // равен ли requests_request_id сообщения requests_request_id
            // позиции, которой принадлежит это сообщение.
            // А requests_request_id позиции у нас пока ещё не поменялся, так 
            // как пока выполняется beforeSave() - он поменяется и сохраниться 
            // дальше.
            if (!$requestMessage->save(false)) {
                throw new \Exception();
            }
        }
        
        // Обновим дочерние позиции.
        $childRequestPositions = RequestPosition::find()
            ->where(['parent_id' => $this->id])
            ->all();
        
        foreach ($childRequestPositions as $childRequestPosition) {
            $childRequestPosition->request_request_id = $newRequestId;
            // Валидацию отключим, так как там проверяется  
            // равен ли requests_request_id дочерней позиции requests_request_id
            // родительсокй позиции.
            // А requests_request_id родительской позиции(т.е. текущей к которой
            // относиться beforeSave) у нас пока ещё не поменялся - он 
            // поменяется и сохраниться дальше.
            if (!$childRequestPosition->save(false)) {
                throw new \Exception();
            }
        }
        
        return true;
    }
    
    private function updateChildPositionAndMessagesWhenDelete()
    {
         // Так как позицию мы удаляем, то установим её в null у всех её сообщений.
        $requestMessages = RequestMessage::find()
            ->where(['requests_request_position_id' => $this->id])
            ->all();
        
        foreach ($requestMessages as $requestMessage) {
            $requestMessage->requests_request_position_id = null;
            if (!$requestMessage->save()) {
                throw new \Exception();
            }
        }
        
        // Так как позицию мы удаляем, то установим в null parent_id у её
        // дочерних позиций.
        $childRequestPositions = RequestPosition::find()
            ->where(['parent_id' => $this->id])
            ->all();
        
        foreach ($childRequestPositions as $childRequestPosition) {
            $childRequestPosition->parent_id = null;
            if (!$childRequestPosition->save()) {
                throw new \Exception();
            }
        }
        
        return true;
    }

    public static function getRequestPositionIdsForRequests($requestIds)
    {
        return self::find()
            ->select(['id'])
            ->where(['in', 'requests_request_id',$requestIds])
            ->column();
    }
    
    public static function addGoodsToRequestPosition($requestPositionId, $goodIds)
    {
        $transaction = self::getDb()->beginTransaction();
        try {
            $requestPosition = self::findOne(['id' => $requestPositionId]);
            if ($requestPosition === null) {
                $transaction->rollBack();
                return false;
            }
            
            $children = $requestPosition->children;
            $existGoodIds = [];
            foreach ($children as $child) {
                if (!empty($child->goods_good_id)) {
                    $existGoodIds []= (int) $child->goods_good_id;
                }
            }
            
            $requestId = $requestPosition->requests_request_id;
            $parentId = $requestPosition->id;
            foreach ($goodIds as $goodId) {
                if (!in_array((int) $goodId, $existGoodIds)) {
                    $newRequestPosition = new self();
                    $newRequestPosition->goods_good_id = $goodId;
                    $newRequestPosition->parent_id = $parentId;
                    $newRequestPosition->requests_request_id = $requestId;
                    if (!$newRequestPosition->save()) {
                        $transaction->rollBack();
                        return false;
                    }
                }
            }
                         
            $transaction->commit();
            return true;
        }
        catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
    
    public static function removeGoodsFromRequestPosition($requestPositionId, $goodIds)
    {
        $transaction = self::getDb()->beginTransaction();
        try {
            $requestPosition = self::findOne(['id' => $requestPositionId]);
            if ($requestPosition === null) {
                $transaction->rollBack();
                return false;
            }
            
            $children = $requestPosition->children;
            foreach ($children as $child) {
                if (
                    !empty($child->goods_good_id) && 
                    in_array((int) $child->goods_good_id, $goodIds)
                ) {
                    if (!$child->delete()) {
                        $transaction->rollBack();
                        return false;
                    }
                }
            }
                         
            $transaction->commit();
            return true;
        }
        catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
}
