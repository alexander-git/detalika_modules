<?php

namespace detalika\auth\models;

use yii\base\Model;
use dektrium\user\helpers\Password;

use detalika\auth\models\User;

class PasswordChangeForm extends Model
{
    public $current_password;
    public $new_password;
    public $new_password_repeat;
       
    private $_user = null;
    
    
    public function __construct(User $user, $config = [])
    {
        $this->_user = $user;
        parent::__construct($config);
    }

    public function formName()
    {
        return 'PasswordChange';
    }

    public function attributeLabels()
    {
        return [
            'current_password' => 'Текущий пароль',
            'new_password' => 'Новый пароль',
            'new_password_repeat' => 'Повторите пароль',
        ];
    }

    public function rules()
    {
        return [
            'new_passwordRequired' => ['new_password', 'required'],
            'new_passwordLength' => [
                'new_password',
                'string', 
                'min' => User::PASSWORD_MIN_LENGTH,
                'max' => User::PASSWORD_MAX_LENGTH,
            ],
            
            'new_password_repeatRequired' => ['new_password_repeat', 'required'],
            'new_password_repeatLength' => [
                'new_password_repeat',
                'string', 
                'min' => User::PASSWORD_MIN_LENGTH,
                'max' => User::PASSWORD_MAX_LENGTH,
            ],
            'new_password_repeatCompare' => [
                'new_password_repeat',
                'compare',
                'compareAttribute' => 'new_password'
            ],
            
            'curent_passwordRequired' => ['current_password', 'required'],
            'currentPasswordValidate' => ['current_password', function ($attr) {
                if (!Password::validate($this->$attr, $this->_user->password_hash)) {
                    $this->addError($attr, 'Текущий пароль введён неверно');
                }
            }],
        ];
    }
    
    public function changePassword()
    {
        if (!$this->validate()) {
            return false;
        }

        $this->_user->scenario = User::SCENARIO_CHANGE_PASSWORD;
        $this->_user->password = $this->new_password;
        return $this->_user->save();
    }
}
