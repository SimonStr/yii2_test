<?php

namespace app\models;

use yii\base\Model;

class RegForm extends Model
{
    public $username;
    public $password;
    public $email;

    public function rules()
    {
        return [
            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => '\app\models\User', 'message' => 'This username has already been taken.'],
            ['username', 'string', 'min' => 2, 'max' => 255],
            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => '\app\models\User', 'message' => 'This email address has already been taken.'],
            ['email', 'string', 'max' => 255],
            ['password', 'required'],
            ['password', 'string', 'min' => 6],
        ];
    }

}