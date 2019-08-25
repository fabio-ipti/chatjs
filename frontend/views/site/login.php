<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use frontend\assets\AppAsset;

$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;
$layoutAsset = frontend\assets\LayoutAsset::register($this);
$layoutBaseUrl = $layoutAsset->baseUrl;
?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <link rel="shortcut icon" type="image/png" href="/images/icons/favicon.png"/>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>

    <!-- Registar Scripts CSS específicos a esta View -->
    <?php $this->registerCssFile(Yii::getAlias('@web').'/css/site/login.css'); ?>

</head>
<body class="hold-transition login-page">
  <?php $this->beginBody() ?>
<div class="login-box">
  <div class="login-logo">
  </div>
  <!-- /.login-logo -->
  <div class="login-box-body">
    <p class="login-box-msg">Digite o Usuário e Senha para iniciar a sessão</p>
    <?php
        foreach($model['errors'] AS $eachField):
          foreach($eachField AS $erro):
      ?>
      <p class="login-box-msg"><?=$erro?></p>
    <?php
          endforeach;
       endforeach;
    ?>


<?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
      <div class="form-group has-feedback">
        <?= Html::activeTextInput($model, 'username',
        ['autofocus' => true, 'type'=>'username', 'class'=>'form-control', 'placeholder'=>'Nome do Usuário']);?>
        <span class="glyphicon glyphicon-user form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <?= Html::activeTextInput($model, 'password',
        ['type'=>'password', 'class'=>'form-control', 'placeholder'=>'Senha']); ?>
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
      <div class="row">
        <div class="col-xs-8">
          <div class="checkbox icheck">
            <!--
            <label id="remember-me">
              <?php // Html::activeTextInput($model, 'rememberMe', ['type'=>'checkbox']); ?>
              Lembre-me
            </label>
          -->

          <label id="add-new-user">
            <?= Html::a('Criar uma conta', ["/user/create-by-invite"]) ?><br>
          </label>

          </div>
        </div>
        <!-- /.col -->
        <div class="col-xs-4">
          <?= Html::submitButton('Entrar', ['class' => 'btn btn-primary btn-block btn-flat', 'name' => 'login-button']) ?>
        </div>
        <!-- /.col -->
      </div>

<?php ActiveForm::end(); ?>
    <?php // Html::a('Esqueci minha senha', ['#']) ?><br>
  </div>
  <!-- /.login-box-body -->
</div>
<!-- /.login-box -->
<!-- Registar Scripts JS específicos a esta View -->
<?php $this->registerJsFile(Yii::getAlias('@web').'/js/site/login.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
