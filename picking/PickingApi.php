<?php

namespace detalika\picking;

use detalika\picking\models\base\ProfileUser;
use detalika\picking\models\base\RequestPositionUser;

class PickingApi 
{
    protected static $clientCache = [];
    protected static $positionCache = [];
    /**
     * Возвращает id профиля клиента взятого в "подбор" комплектовщиком. Если 
     * в данный момент нет клиента на подборе текущим комплектовщиком 
     * возвращает null.
     * @param integer $pickerId Id комплектовщика.
     * @return integer
     */
    public static function getPickingClientProfileIdByPickerId($pickerId)
    {
        if (isset(self::$clientCache[$pickerId])) {
            return self::$clientCache[$pickerId];
        }

        $model = ProfileUser::find()
            ->where(['user_id' => $pickerId])
            ->one();

        if ($model === null) {
            $result = null;
        } else {
            $result = $model->clients_profile_id;
        }

        return self::$clientCache[$pickerId] = $result;
    }
    
    /**
     * Возвращает id позиции запроса взятой в "подбор" комплектовщиком. Если 
     * в данный момент нет позиции запроса на подборе текущим комплектовщиком 
     * возвращает null.
     * @param integer $pickerId Id комплектовщика.
     * @return integer
     */
    public static function getPickingRequestPositionIdByPickierId($pickerId)
    {
        if (isset(self::$positionCache[$pickerId])) {
            return self::$positionCache[$pickerId];
        }

        $model = RequestPositionUser::find()
            ->where(['user_id' => $pickerId])
            ->one();

        if ($model === null) {
            $result = null;
        } else {
            $result = $model->requests_request_position_id;
        }

        return self::$positionCache[$pickerId] = $result;
    }

    public function isPickingProfile($checkedProfileId)
    {
        $userId = $this->getUserId();
        if ($userId === null) {
            return false;
        }

        return ProfileUser::find()
                ->andWhere([
                    'user_id' => $userId,
                    'clients_profile_id' => $checkedProfileId,
                ])->count() > 0;
    }
}
