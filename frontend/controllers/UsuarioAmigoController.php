<?php

namespace frontend\controllers;

use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\components\AccessRule;
use frontend\models\Usuario;
use yii\db\Query;
use frontend\models\UsuarioAmigo;


class UsuarioAmigoController extends \yii\web\Controller
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
              'only' => ['index'],
              'rules' => [
                  [
                      'actions' => ['index', 'get-all-friends', 'add-friend'],
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

    public function actionGetAllFriends()
    {
        if(!(isset($_GET["codigo"]) && !empty($_GET["codigo"]))){
          echo $_GET['callback']."(".json_encode([0, "Precisa Indicar o seu código"]).");";
        }else{
          if (!isset($_GET["usuario_id"])){
            echo $_GET['callback']."(".json_encode([0, "Precisa Indicar o id do usuário"]).");";
          }else{
            //$user = Usuario::find()->where(['id'=>$_GET["id"]])->one();
            $queryGetUser = (new Query())->select('u.id AS id, p.nome AS nome, p.data_nasc AS data_nasc,
             p.email AS email, p.codigo AS codigo, u.usuario AS usuario, u.senha AS senha')
            ->from('pessoa AS p')
            ->join('INNER JOIN','usuario AS u', 'u.pessoa_id = p.id')
            ->join('INNER JOIN','usuario_amigo AS a', 'a.amigo_id = u.id')
            ->where(['a.usuario_id'=>$_GET["usuario_id"], 'p.codigo'=>$_GET["codigo"]]);
            $users = $queryGetUser->createCommand()->queryAll();
            if(count($users) > 0){
                echo $_GET['callback']."(".json_encode($users).");";
            }else{
                echo $_GET['callback']."(".json_encode([1, "Não encontrado"]).");";
            }

          }

        }
    }

    public function actionAddFriend()
    {
        if(!(isset($_GET["codigo"]) && !empty($_GET["codigo"]))){
          echo $_GET['callback']."(".json_encode([0, "Precisa Indicar o seu código"]).");";
        }else{
          if (!isset($_GET["usuario_id"], $_GET["amigo_id"])){
            echo $_GET['callback']."(".json_encode([0, "Precisa Indicar o id do usuário e do amigo"]).");";
          }else{
            $queryGetUserAmigo = (new Query())->select('count(a.id) AS numFriends')
            ->from('pessoa AS p')
            ->join('INNER JOIN','usuario AS u', 'u.pessoa_id = p.id')
            ->join('INNER JOIN','usuario_amigo AS a', 'a.usuario_id = u.id')
            ->where(['p.codigo'=>$_GET["codigo"], 'a.usuario_id'=>$_GET["usuario_id"],
                    'a.amigo_id'=>$_GET["amigo_id"]]);
            $isFriend = $queryGetUserAmigo->createCommand()->queryOne();
            if($isFriend['numfriends'] == 0){
              $usuario = Usuario::find()->where(['id'=>$_GET["usuario_id"]])->One();
              $amigo = Usuario::find()->where(['id'=>$_GET["amigo_id"]])->One();

              if(ISSET($usuario, $amigo) && $usuario->getPessoa()->One()->codigo == $_GET["codigo"]  &&
                  $usuario->getPessoa()->One()->codigo == $amigo->getPessoa()->One()->codigo){
                $ua = new UsuarioAmigo();
                $ua->usuario_id = $_GET["usuario_id"];
                $ua->amigo_id = $_GET["amigo_id"];
                if($ua->save()){
                  echo $_GET['callback']."(".json_encode(["Adicionado!"]).");";
                }else{
                  echo $_GET['callback']."(".json_encode([1, "Erro ao adicionar"]).");";
                }
              }else{
                  echo $_GET['callback']."(".json_encode([0, "Os usuários devem pertencer ao mesmo código de acesso a API"]).");";
              }

            }else{
              echo $_GET['callback']."(".json_encode([0, "Amigo já adicionado"]).");";
            }

          }

        }
    }



}
