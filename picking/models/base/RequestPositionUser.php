<?php

namespace detalika\picking\models\base;

use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "picking_request_position_users".
 *
 * @property integer $id
 * @property string $created
 * @property string $updated
 * @property string $requests_request_position_id
 * @property string $user_id
 */
class RequestPositionUser extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'picking_request_position_users';
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
            ['requests_request_position_id', 'required'],
            ['requests_request_position_id', 'integer'],
            
            ['user_id', 'required'],
            ['user_id', 'integer'],
            
            [
                ['requests_request_position_id', 'user_id'], 
                'unique', 
                'targetAttribute' => ['requests_request_position_id', 'user_id'], 
            ],
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
            'requests_request_position_id' => 'Позиция запроса',
            'user_id' => 'Пользователь',
        ];
    }
    
    public static function startPickingForRequestPosition($requestPositionId, $userId) 
    {
        $transaction = self::getDb()->beginTransaction();
        try {
            $requestPositionUser = self::find()
                ->where([
                    'requests_request_position_id' => $requestPositionId,
                    'user_id' => $userId,
                ])
                ->one();
            
            if ($requestPositionUser === null) {
                $requestPositionUser = new self();
                $requestPositionUser->requests_request_position_id = $requestPositionId;
                $requestPositionUser->user_id = $userId;
                if (!$requestPositionUser->save()) {
                    $transaction->rollBack();
                    return false;
                }
            }
                    
            $requestPositionUsersToDelete = self::find()
                ->where(['user_id' => $userId])
                ->andWhere(['<>', 'requests_request_position_id', $requestPositionId])
                ->all();
            
            foreach ($requestPositionUsersToDelete as $requestPositionUserToDelete) {
                if (!$requestPositionUserToDelete->delete()) {
                    $transaction->rollBack();
                    return false;
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
    
    public static function stopPickingForRequestPosition($requestPositionId, $userId)
    {
        $requestPositionUser = self::find()
            ->where([
                'requests_request_position_id' => $requestPositionId,
                'user_id' => $userId,
            ])
            ->one();

        if ($requestPositionUser === null) {
            // Подбор и так не идёт.
            return true;    
        }
        
        if (!$requestPositionUser->delete()) {
            return false;
        }
        
        return true;
    }
}
