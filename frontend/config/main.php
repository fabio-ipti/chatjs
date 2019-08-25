<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'components' => [

/* DESABILITAR JS, CSS DO bootstrap do YII
      'assetManager' => [
           'bundles' => [
              'yii\web\JqueryAsset' => [
                  'js'=>[]
              ],
              'yii\bootstrap\BootstrapPluginAsset' => [
                  'js'=>[]
              ],
              'yii\bootstrap\BootstrapAsset' => [
                  'css' => [],
              ],
         ],
      ],
*/

        'request' => [
            'csrfParam' => '_csrf-frontend',
            'enableCsrfValidation' => false,
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'loginUrl' => ['site/login'],
            'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'advanced-frontend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],

        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
          /*  'rules' => [

          ], */
        ],
             'mailer' => [
              'class' => 'yii\swiftmailer\Mailer',
              'viewPath' => '@frontend/mail',
              'useFileTransport' => false,
              'transport' => [
                  'class' => 'Swift_SmtpTransport',
                  'host' => 'smtp.gitedu.com.br',
                  'username' => 'contato@gitedu.com.br',
                  'password' => 'am@n1992',
                  'port' => '587',
              ],
          ],


    ],
    'params' => $params,
];
