<?php

namespace detalika\picking\models\base;

use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "picking_profile_users".
 *
 * @property integer $id
 * @property string $created
 * @property string $updated
 * @property string $clients_profile_id
 * @property string $user_id
 */
class ProfileUser extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'picking_profile_users';
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
            ['clients_profile_id', 'required'],
//            ['clients_profile_id', 'integer'],
            
            ['user_id', 'required'],
            ['user_id', 'integer'],
            
            [
                ['clients_profile_id', 'user_id'], 
                'unique', 
                'targetAttribute' => ['clients_profile_id', 'user_id'], 
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
            'clients_profile_id' => 'Профиль клинта',
            'user_id' => 'Пользователь',
        ];
    }
    
    public static function startPickingForProfile($clientProfileId, $userId) 
    {
        $transaction = self::getDb()->beginTransaction();
        try {
            $profileUser = self::find()
                ->where([
                    'clients_profile_id' => $clientProfileId,
                    'user_id' => $userId,
                ])
                ->one();
            
            if ($profileUser === null) {
                $profileUser = new self();
                $profileUser->clients_profile_id = $clientProfileId;
                $profileUser->user_id = $userId;
                if (!$profileUser->save()) {
                    $transaction->rollBack();
                    return false;
                }
            }
                    
            $profileUsersToDelete = self::find()
                ->where(['user_id' => $userId])
                ->andWhere(['<>', 'clients_profile_id', $clientProfileId])
                ->all();
            
            foreach ($profileUsersToDelete as $profileUserToDelete) {
                if (!$profileUserToDelete->delete()) {
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
    
    public static function stopPickingForProfile($clientProfileId, $userId)
    {
        $profileUser = self::find()
            ->where([
                'clients_profile_id' => $clientProfileId,
                'user_id' => $userId,
            ])
            ->one();

        if ($profileUser === null) {
            // Подбор и так не идёт.
            return true;    
        }
        
        if (!$profileUser->delete()) {
            return false;
        }
        
        return true;
    }
}
