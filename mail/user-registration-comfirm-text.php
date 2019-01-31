<?php
use yii\helpers\Html;

/* @var $user app\models\User  */

$confirmLink = Yii::$app->urlManager->createAbsoluteUrl(['site/confirm', 'token' => $user->email_confirm_token]);
?>
    Hello <?= $user->username ?>,

    Follow the link below to confirm your email:

<?= Html::decode($confirmLink) ?>