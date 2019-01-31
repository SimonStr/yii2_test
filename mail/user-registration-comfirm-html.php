<?php
use yii\helpers\Html;

/* @var $user app\models\User  */

$confirmLink = Yii::$app->urlManager->createAbsoluteUrl(['site/confirm&token=', 'token' => $user->email_confirm_token]);
?>
<div>
    <p>Hello <?= Html::decode($user->username) ?>,</p>

    <p>Follow the link below to confirm your email:</p>

    <p><?= Html::a(Html::decode($confirmLink), $confirmLink) ?></p>
</div>