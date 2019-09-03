<?php

namespace frontend\controllers;

use frontend\models\Pessoa;
use frontend\models\Usuario;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\components\AccessRule;
use yii\db\Query;

class UsuarioController extends \yii\web\Controller
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
              'only' => ['index',],
              'rules' => [
                  [
                      'actions' => ['index', 'get-all-users',  'get-user'],
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
      if(!(isset($_GET["codigo"]) && !empty($_GET["codigo"]))){
        echo $_GET['callback']."(".json_encode([0, "Precisa Indicar o seu código"]).");";
      }else{
          $queryGetUser = (new Query())->select('u.id AS id, p.nome AS nome, p.data_nasc AS data_nasc,
           p.email AS email, p.codigo AS codigo, u.usuario AS usuario, u.senha AS senha')
          ->from('pessoa AS p')
          ->join('INNER JOIN','usuario AS u', 'u.pessoa_id = p.id')
          ->where(['p.codigo'=>$_GET["codigo"]]);
          $users = $queryGetUser->createCommand()->queryAll();
          if(count($users) > 0){
              echo $_GET['callback']."(".json_encode($users).");";
          }else{
              echo $_GET['callback']."(".json_encode([1, "Nenhum usuário encontrado"]).");";
          }
      }
    }

    public function actionGetUser()
    {
        if(!(isset($_GET["codigo"]) && !empty($_GET["codigo"]))){
          echo $_GET['callback']."(".json_encode([0, "Precisa Indicar o seu código"]).");";
        }else{
          if (!isset($_GET["id"])){
            echo $_GET['callback']."(".json_encode([0, "Precisa Indicar o id"]).");";
          }else{
            //$user = Usuario::find()->where(['id'=>$_GET["id"]])->one();
            $queryGetUser = (new Query())->select('u.id AS id, p.nome AS nome, p.data_nasc AS data_nasc,
             p.email AS email, p.codigo AS codigo, u.usuario AS usuario, u.senha AS senha')
            ->from('pessoa AS p')
            ->join('INNER JOIN','usuario AS u', 'u.pessoa_id = p.id')
            ->where(['u.id'=>$_GET["id"], 'p.codigo'=>$_GET["codigo"]]);
            $user = $queryGetUser->createCommand()->queryOne();
            if(ISSET($user["id"])){
                echo $_GET['callback']."(".json_encode($user).");";
            }else{
                echo $_GET['callback']."(".json_encode([1, "Não encontrado"]).");";
            }

          }

        }
    }

    public function actionGetUserByName()
    {
        if(!(isset($_GET["codigo"]) && !empty($_GET["codigo"]))){
          echo $_GET['callback']."(".json_encode([0, "Precisa Indicar o seu código"]).");";
        }else{
          if (!isset($_GET["id"])){
            echo $_GET['callback']."(".json_encode([0, "Precisa Indicar o id"]).");";
          }else{
            //$user = Usuario::find()->where(['id'=>$_GET["id"]])->one();
            $queryGetUser = (new Query())->select('u.id AS id, p.nome AS nome, p.data_nasc AS data_nasc,
             p.email AS email, p.codigo AS codigo, u.usuario AS usuario, u.senha AS senha')
            ->from('pessoa AS p')
            ->join('INNER JOIN','usuario AS u', 'u.pessoa_id = p.id')
            ->where(['u.id'=>$_GET["id"], 'p.codigo'=>$_GET["codigo"]]);
            $user = $queryGetUser->createCommand()->queryOne();
            if(ISSET($user["id"])){
                echo $_GET['callback']."(".json_encode($user).");";
            }else{
                echo $_GET['callback']."(".json_encode([1, "Não encontrado"]).");";
            }

          }

        }
    }

    public function actionAddUser()
    {

        if(!(isset($_GET["codigo"]) && !empty($_GET["codigo"]))){
          echo $_GET['callback']."(".json_encode([0, "Precisa Indicar o seu código"]).");";
        }else{
          if (!isset($_GET["nome"], $_GET["data_nasc"], $_GET["email"], $_GET["usuario"], $_GET["senha"])){
            echo $_GET['callback']."(".json_encode([0, "Indique todos as informações do usuário"]).");";
          }else{
            try {
              $pessoa = new Pessoa();
              $user = new Usuario();
              $pessoa->nome = $_GET["nome"];
              $pessoa->data_nasc = $_GET["data_nasc"];
              $pessoa->email = $_GET["email"];
              $pessoa->codigo = $_GET["codigo"];
              if(!$pessoa->save()){
                  echo $_GET['callback']."(".json_encode([1, "Erro ao adicionar"]).");";
              }else{
                  $user->pessoa_id = $pessoa->id;
                  $user->usuario = $_GET["usuario"];
                  $user->senha = $_GET["senha"];
                  if($user->save()){
                      echo $_GET['callback']."(".json_encode(["Adicionado!"]).");";
                  }else{
                      echo $_GET['callback']."(".json_encode([1, "Erro ao adicionar"]).");";
                  }
              }
            }catch(Exception $e){
              echo $e->message();
            }

          }

        }
    }

}
