<?php
/**
 * Created by PhpStorm.
 * User: execut
 * Date: 6/26/17
 * Time: 3:10 PM
 */

namespace detalika\auth\adapter;


use detalika\auth\models\User;
use detalika\clients\models\Profile;
use detalika\clients\OuterDependenciesInterface;
use yii\base\Component;

class Db extends Component
{
    public function hasByContactValues($contactValue) {
        $profile = $this->findByContactValues($contactValue);
        return !empty($profile) && !empty($profile->user_id);
    }

    public function getCurrentProfile() {
        return \yii::$container->get(OuterDependenciesInterface::class)->getCurrentProfile();
    }

    /**
     * @param $contactValue
     * @return mixed
     */
    public function findByContactValues($contactValue)
    {
        $profile = Profile::find()->byContactValue($contactValue)->one();
        return $profile;
    }

    public function save($profile) {
        if (!$profile->save()) {
            foreach ($profile->contacts as $contact) {
                var_dump($contact->attributes);
                echo '<br>';
            }
            var_dump($profile->errors);
            exit;
            return false;
        }

        return true;
    }

    public function setUserProfile($user, $profile) {
        $user->setProfile($profile);
    }

    public function findUserByContactValue($value) {
        $profile = $this->findByContactValues($value);
        if ($profile) {
            return User::findOne([
                'id' => $profile->user_id
            ]);
        }
    }
}