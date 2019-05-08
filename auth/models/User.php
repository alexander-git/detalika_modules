<?php

namespace detalika\auth\models;

use dektrium\user\models\User as BaseUser;
use detalika\clients2\adapter\OData;

class User extends BaseUser 
{
    const SCENARIO_CLIENT_REGISTRATION = 'clientRegistration';
    const SCENARIO_UPDATE_BY_CLIENT = 'updateByClient';
    const SCENARIO_CHANGE_PASSWORD = 'changePassword';
    
    const PASSWORD_MIN_LENGTH = 6;
    const PASSWORD_MAX_LENGTH = 72;


    /**
     * This method is used to register new user account. If Module::enableConfirmation is set true, this method
     * will generate new confirmation token and use mailer to send it to the user.
     *
     * @return bool
     */
    public function register()
    {
        if ($this->getIsNewRecord() == false) {
            throw new \RuntimeException('Calling "' . __CLASS__ . '::' . __METHOD__ . '" on existing user');
        }

        $transaction = $this->getDb()->beginTransaction();

        try {
            $this->confirmed_at = $this->module->enableConfirmation ? null : time();
            $this->password     = $this->module->enableGeneratingPassword ? Password::generate(8) : $this->password;

            $this->trigger(self::BEFORE_REGISTER);

            if (!$this->save()) {
                $transaction->rollBack();
                return false;
            }

            if ($this->module->enableConfirmation) {
                /** @var Token $token */
                $token = \Yii::createObject(['class' => Token::className(), 'type' => Token::TYPE_CONFIRMATION]);
                $token->link('user', $this);
            }

//            $this->mailer->sendWelcomeMessage($this, isset($token) ? $token : null);
            $this->trigger(self::AFTER_REGISTER);

            $transaction->commit();

            return true;
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::warning($e->getMessage());
            throw $e;
        }
    }
    
    public function rules()
    {
        $rules = parent::rules();
        unset($rules['usernameRequired']);
        unset($rules['usernameUnique']);
        
        $rules['passwordLength'] = [
            'password', 
            'string', 
            'min' => self::PASSWORD_MIN_LENGTH, 
            'max' => self::PASSWORD_MAX_LENGTH, 
            'on' => ['register', 'create', self::SCENARIO_CLIENT_REGISTRATION, self::SCENARIO_CHANGE_PASSWORD],
        ];

        $rules[] = ['clients_profile_id', 'safe'];
        
        return $rules;
    }
        
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CHANGE_PASSWORD] = [
            'password',
        ];

        $scenarios[self::SCENARIO_CLIENT_REGISTRATION] = [
            'email',
            'password',
        ];
        
        $scenarios[self::SCENARIO_UPDATE_BY_CLIENT] = ['email'];
        
        return $scenarios;
    }

    /**
     * @return OData
     */
    public function getAdapter() {
        /**
         * @var OData $adapter
         */
        return \yii::$app->profileAdapter;
    }

    public function getProfile()
    {
        return $this->getAdapter()->findProfileByUserId($this->id);
    }
}
