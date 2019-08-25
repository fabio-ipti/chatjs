<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'console\controllers',
    'components' => [
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'mailer' => [
         'class' => 'yii\swiftmailer\Mailer',
         'viewPath' => '@frontend/mail',
         'useFileTransport' => false,
         'transport' => [
             'class' => 'Swift_SmtpTransport',
             'host' => 'smtp.gitedu.com.br',
             'username' => 'contato@gitedu.com.br',
             'password' => '',
             'port' => '587',
         ],
     ],
    ],
    'params' => $params,
];
