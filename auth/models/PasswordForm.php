<?php
/**
 * Created by PhpStorm.
 * User: execut
 * Date: 5/26/17
 * Time: 11:36 AM
 */

namespace detalika\auth\models;


use kartik\detail\DetailView;
use yii\base\Model;

class PasswordForm extends Model
{
    public $password = null;
    public $password_repeat = null;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // password rules
            'passwordRequired' => [['password', 'password_repeat'], 'required'],
            'passwordEquals' => [['password'], 'checkPasswordsEquals'],
            'passwordLength'   => [['password', 'password_repeat'], 'string', 'min' => 6, 'max' => 72],
        ];
    }

    public function checkPasswordsEquals() {
        if ($this->password !== $this->password_repeat) {
            $this->addError('Пароль и повтор пароля должны совпадать');
            return false;
        }

        return true;
    }

    public function getFormFields() {
        return [
            [
                'attribute' => 'password',
                'type' => DetailView::INPUT_PASSWORD,
                'editModel' => $this,
                'viewModel' => $this,
            ],
            [
                'attribute' => 'password_repeat',
                'type' => DetailView::INPUT_PASSWORD,
                'editModel' => $this,
                'viewModel' => $this,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'password' => 'Пароль',
            'password_repeat' => 'Повтор пароля',
        ];
    }
}