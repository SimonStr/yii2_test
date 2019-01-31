<?php

namespace app\models;

use Yii;
use app\models\User;
use app\models\RegForm;
use yii\base\Model;

class UserServise
{
    public function saveUser(RegForm $form)
    {
        $user = new User();
        $user->username = $form->username;
        $user->generateAuthKey();
        $user->setPassword($form->password);
        $user->email = $form->email;
        $user->email_confirm_token = Yii::$app->security->generateRandomString();
        $user->is_active = User::STATUS_WAIT;

        if($user->save()){
            return $user;
        } else {
            throw new \RuntimeException('Saving error.');
        }

    }

    public function updateUser($form, $user)
    {
        $user->username = $form->username;
        $user->generateAuthKey();
        if (isset($form->password)) {
            $user->setPassword($form->password);
        }
        $user->email = $form->email;

        if($user->save()){
            return true;
        } else {
            throw new \RuntimeException('Saving error.');
        }
    }


    public function sentEmailConfirm(User $user)
    {
        $email = $user->email;

        $sent = Yii::$app->mailer
            ->compose(
                ['html' => 'user-registration-comfirm-html','text' => 'user-registration-comfirm-text',],
                ['user' => $user])
            ->setTo($email)
            ->setFrom(Yii::$app->params['adminEmail'])
            ->setSubject('Confirmation of registration')
            ->send();
        if (!$sent) {
            throw new \RuntimeException('Sending error.');
        }
    }


    public function confirmation($token): void
    {
        if (empty($token)) {
            throw new \DomainException('Empty confirm token.');
        }

        $user = User::findOne(['email_confirm_token' => $token]);
//        var_dump($user);
//        die();
        if (!$user) {
            throw new \DomainException('User is not found.');
        }

        $user->email_confirm_token = null;
        $user->is_active = User::STATUS_ACTIVE;
        if (!$user->save()) {
            throw new \RuntimeException('Saving error.');
        }

        if (!Yii::$app->getUser()->login($user)){
            throw new \RuntimeException('Error authentication.');
        }
    }
}