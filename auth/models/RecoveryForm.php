<?php

namespace detalika\auth\models;

use dektrium\user\models\RecoveryForm as BaseRecoveryForm;

class RecoveryForm extends BaseRecoveryForm
{
    public $password_repeat;
       
    public function attributeLabels()
    {
        $labels = parent::attributeLabels();
        $labels['password_repeat'] = 'Повторите пароль';
        return $labels;
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_RESET] = [
            'password', 
            'password_repeat'
        ];
        
        return $scenarios;
    }

    public function rules()
    {
        $rules = parent::rules();
        $rules['passwordLength'] = [
            'password',
            'string', 
            'min' => User::PASSWORD_MIN_LENGTH,
            'max' => User::PASSWORD_MAX_LENGTH,
        ];
        $rules['password_repeatRequired'] = ['password_repeat', 'required'];
        $rules['password_repeatLength'] = [
            'password_repeat',
            'string', 
            'min' => User::PASSWORD_MIN_LENGTH,
            'max' => User::PASSWORD_MAX_LENGTH,
        ];
        $rules['password_repeatCompare'] = [
            'password_repeat',
            'compare',
            'compareAttribute' => 'password'
        ];
        
        return $rules;
    }
}
