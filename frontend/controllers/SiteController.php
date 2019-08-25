<?php
namespace frontend\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use common\components\AccessRule;
use common\models\User;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                // Deve sobreescrever as configurações padrões de regras com a nova Class AccessRule
                'ruleConfig' => [
                    'class' => AccessRule::className(),
                ],
                'only' => ['login', 'logout', 'signup', 'index', 'about', 'get-all-users'],
                'rules' => [
                    [
                        'actions' => ['signup', 'login','?', 'get-all-users'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout','index', 'about'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['studentdashboard'],
                        'allow' => true,
                        'roles' => [User::ROLE_STUDENT],
                    ],
                    [
                        'actions' => ['teacherdashboard'],
                        'allow' => true,
                        'roles' => [User::ROLE_TEACHER, User::ROLE_ADMIN],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['get'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
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
     * @return mixed
     */
    public function actionIndex()
    {
      $this->render('site/index');
    }


    public function actionGetAllUsers()
    {
      var_dump($_REQUEST);exit();
    }






    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
      $this->layout = false;
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
          //LOGGED !!!
            if(!ISSET(Yii::$app->user->identity->glab_private_token)){
                //Obtém o private_token no gitlab
                $username = Yii::$app->request->post()['LoginForm']['username'];
                $password = Yii::$app->request->post()['LoginForm']['password'];
                $sessionGlab = Yii::$app->runAction('git-lab/getsession',
                  ['username'=> $username, 'password'=> $password]);

                Yii::$app->user->identity->glab_private_token = $sessionGlab["private_token"];
                //Atualiza o Usuário no gitEdu, com o seu private_token
                $logUser = \frontend\models\User::find()->where(['id'=>Yii::$app->user->identity->id])->one();
                $logUser->glab_private_token = Yii::$app->user->identity->glab_private_token;
                $logUser->save();
            }
            //Obtém o Gravatar e deixar a URL correspondente no cookie
            $glabUser = Yii::$app->runAction('git-lab/getme');
            $cookies = Yii::$app->response->cookies;
            $cookies->add(new \yii\web\Cookie([
                'name' => 'avatar_url',
                'value' => $glabUser["avatar_url"],
                'expire' => time()+60*60*24*30,
            ]));

          return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionDefaultContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending email.');
            }

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
      $this->layout = false;
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for email provided.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password was saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }
}
