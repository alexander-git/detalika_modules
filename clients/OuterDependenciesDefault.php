<?php

namespace detalika\clients;

use detalika\clients\models\outer\ClientCarProfile;
use detalika\clients\models\outer\Order;
use detalika\clients\models\outer\Request;
use detalika\clients\models\User;

class OuterDependenciesDefault implements OuterDependenciesInterface
{
    /**
     * @deprecated
     */
    public function getSourcesTableName() {return 'sources';}
    /**
     * @deprecated
     */
    public function getSourcesIdFieldName() {return 'id';}
    /**
     * @deprecated
     */
    public function getSourcesNameFieldName() {return 'name';}

    public function getShopsTableName() {return 'shops';}
    public function getShopsIdFieldName() {return 'id'; }
    public function getShopsNameFieldName() {return 'name'; }
    
    public function getUsersTableName() {return 'user';}
    public function getUsersIdFieldName() {return 'id';}
    public function getUsersEmailFieldName() {return 'email';}
    
        
    public function getPickingProfileUsersTableName() {return 'picking_profile_users';}
    public function getPickingProfileUsersProfileIdFieldName() {return 'clients_profile_id';}
    public function getPickingProfileUsersUserIdFieldName() {return 'user_id';}

    public function getAuthUsersAjaxRoute() 
    {
        return ['/auth/user/index']; 
    }

    public function getOrdersTableName() {
        return 'orders';
    }

    public function getOrdersIdFieldName() {
        return 'id';
    }

    public function getOrdersProfileIdFieldName() {
        return 'clients_profile_id';
    }

    public function getOrdersModelClass() {
        return Order::class;
    }

    public function getRequestsTableName() {
        return 'requests_requests';
    }

    public function getRequestsIdFieldName() {
        return 'id';
    }

    public function getRequestsProfileIdFieldName() {
        return 'clients_profile_id';
    }

    public function getRequestsModelClass() {
        return Request::class;
    }

    public function getRequestsClientCarProfilesTableName() {
        return 'requests_client_car_profiles';
    }

    public function getRequestsClientCarProfilesIdFieldName() {
        return 'id';
    }

    public function getRequestsClientCarProfilesProfileIdFieldName() {
        return 'clients_profile_id';
    }

    public function getRequestsClientCarProfilesModelClass() {
        return ClientCarProfile::class;
    }

    protected $_currentProfile = null;

    /**
     * @return bool|mixed
     */
    public function getCurrentProfile()
    {
        if ($this->_currentProfile !== null) {
            return $this->_currentProfile;
        }

        $userId = \Yii::$app->user->id;
        $userIdFieldName = User::getFieldName('id');
        $user = User::findOne([$userIdFieldName => $userId]);
        if ($user && ($profileModel = $user->profile)) {
            return $this->_currentProfile = $profileModel;
        }

        return $this->_currentProfile = false;
    }
}
