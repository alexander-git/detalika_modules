<?php
/**
 * Created by PhpStorm.
 * User: execut
 * Date: 6/7/17
 * Time: 4:18 PM
 */

namespace detalika\auth;


use detalika\auth\adapter\Db;
use detalika\auth\models\User;
use detalika\clients\models\ContactType;
use detalika\clients\models\Profile;

class Finder extends \dektrium\user\Finder
{

    /**
     * Finds a user by the given email.
     *
     * @param string $email Email to be used on search.
     *
     * @return models\User
     */
    public function findUserByUsernameOrEmail($email)
    {
        if ($user = parent::findUserByUsernameOrEmail($email)) {
            return $user;
        }

        return $this->findUserByProfileContacts($email);
    }

    public function findUserByProfileContacts($email) {
        /**
         * @var Db $adapter
         */
        $adapter = \yii::$app->profileAdapter;
        return $adapter->findUserByContactValue([
            ['type' => ContactType::TYPE_EMAIL, 'value' => $email],
            ['type' => ContactType::TYPE_PHONE, 'value' => preg_replace('/[^\d]+/i', '', $email)],
        ]);
    }
}