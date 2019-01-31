<?php

namespace app\controllers;

use app\models\UserServise;
use app\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\RegForm;
use app\models\ProfileForm;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionRegistration()
    {
        $model = new RegForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $userServise = new UserServise();


            try{
                $user = $userServise->saveUser($model);
                $userServise->sentEmailConfirm($user);
                Yii::$app->session->setFlash('success', 'Check your email to confirm the registration.');
                return $this->goHome();
            } catch (\RuntimeException $e){
                Yii::$app->errorHandler->logException($e);
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }

        return $this->render('registration', [
            'model' => $model,
        ]);
    }

    public function actionConfirm($token)
    {
        $userServise = new UserServise();

        try{
            $userServise->confirmation($token);
            Yii::$app->session->setFlash('success', 'You have successfully confirmed your registration.');
        } catch (\Exception $e){
            Yii::$app->errorHandler->logException($e);
            Yii::$app->session->setFlash('error', $e->getMessage());
        }

        return $this->goHome();
    }

    public function actionProfile() {

        if (!Yii::$app->user->isGuest) {

            $form = new ProfileForm();
            $user = User::findOne(['id' => Yii::$app->user->id]);
            if ($form->load(Yii::$app->request->post()) && $form->validate()) {
                $userServise = new UserServise();

                try{
                    $userServise->updateUser($form, $user);
                    Yii::$app->session->setFlash('success', 'User fields update.');
                    return $this->refresh();
                } catch (\RuntimeException $e){
                    Yii::$app->errorHandler->logException($e);
                    Yii::$app->session->setFlash('error', $e->getMessage());
                }
            }

            $form->username = $user->username;
            $form->email = $user->email;

            return $this->render('profile', [
                'model' => $form,
            ]);
        } else {
            return $this->goHome();

        }
    }


}
