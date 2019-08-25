<?php

namespace frontend\controllers;

use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\components\AccessRule;


class PessoaController extends \yii\web\Controller
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
              'only' => ['index', 'get-all-person'],
              'rules' => [
                  [
                      'actions' => ['index', 'get-all-users'],
                      'allow' => true,
                      'roles' => ['?'],
                  ],
                  [
                      'actions' => ['about'],
                      'allow' => true,
                      'roles' => ['@'],
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


    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionGetAllUsers()
    {
        var_dump($_REQUEST);exit();
    }

}
