<?php

namespace frontend\controllers;

use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\components\AccessRule;
use frontend\models\Mensagem;
use frontend\models\Usuario;
use frontend\models\UsuarioAmigo;


class MensagemController extends \yii\web\Controller
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
                      'actions' => ['index', 'add-msg', 'get-msgs', 'get-msg-from-remetente_id', 'get-msg-from-receptor_id'],
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

    public function actionAddMsg()
    {
        if(!(isset($_GET["codigo"]) && !empty($_GET["codigo"]))){
          echo json_encode([0, "Precisa Indicar o seu código"]);
        }else{
          if (!isset($_GET["remetente_id"], $_GET["receptor_id"], $_GET["mensagem"], $_GET["data"])){
            echo json_encode([0, "Precisa Indicar o remetente, receptor, mensagem e a data"]);
          }else{
              $remetente = Usuario::find()->where(['id'=>$_GET["remetente_id"]])->One();
              $receptor = Usuario::find()->where(['id'=>$_GET["receptor_id"]])->One();
              if(ISSET($remetente, $receptor) && $remetente->getPessoa()->One()->codigo == $_GET["codigo"]  &&
                  $remetente->getPessoa()->One()->codigo == $receptor->getPessoa()->One()->codigo){
                $ua = UsuarioAmigo::find()->where(['usuario_id'=>$remetente->id, 'amigo_id'=>$receptor->id])->One();
                if(ISSET($ua)){
                  $mensagem = new Mensagem();
                  $mensagem->remetente_id = $remetente->id;
                  $mensagem->receptor_id = $receptor->id;
                  $mensagem->data = $_GET["data"];
                  $mensagem->mensagem = $_GET["mensagem"];
                  if($mensagem->save()){
                    echo json_encode(["Mensagem Enviada!"]);
                  }else{
                    echo json_encode([1, "Erro ao enviar mensagem"]);
                  }
                }else{
                  echo json_encode([0, "Os usuários devem ser Amigos"]);
                }
              }else{
                  echo json_encode([0, "Os usuários devem pertencer ao mesmo código de acesso a API"]);
              }

          }

        }
    }

    public function actionGetMsgs()
    {
        if(!(isset($_GET["codigo"]) && !empty($_GET["codigo"]))){
          echo json_encode([0, "Precisa Indicar o seu código"]);
        }else{
          if (!isset($_GET["remetente_id"], $_GET["receptor_id"])){
            echo json_encode([0, "Precisa Indicar o remetente_id e o receptor_id"]);
          }else{
            $remetente = Usuario::find()->where(['id'=>$_GET["remetente_id"]])->One();
            $receptor = Usuario::find()->where(['id'=>$_GET["receptor_id"]])->One();
            if(ISSET($remetente, $receptor) && $remetente->getPessoa()->One()->codigo == $_GET["codigo"]  &&
                $remetente->getPessoa()->One()->codigo == $receptor->getPessoa()->One()->codigo){
                $queryGetMessages = Mensagem::find()->where(['remetente_id'=>$remetente->id, 'receptor_id'=>$receptor->id]);
                $msgs = $queryGetMessages->createCommand()->queryAll();
                if(count($msgs) > 0){
                    echo json_encode($msgs);
                }else{
                    echo json_encode([0, "Não existem mensagens"]);
                }
                }else{
                    echo json_encode([0, "Os usuários devem pertencer ao mesmo código de acesso a API"]);
                }
          }

        }
    }

    public function actionGetMsgsFromRemetenteId()
    {
        if(!(isset($_GET["codigo"]) && !empty($_GET["codigo"]))){
          echo json_encode([0, "Precisa Indicar o seu código"]);
        }else{
          if (!isset($_GET["remetente_id"])){
            echo json_encode([0, "Precisa Indicar o remetente_id"]);
          }else{
            $remetente = Usuario::find()->where(['id'=>$_GET["remetente_id"]])->One();
            if(ISSET($remetente) && $remetente->getPessoa()->One()->codigo == $_GET["codigo"]){
                $queryGetMessages = Mensagem::find()->where(['remetente_id'=>$remetente->id]);
                $msgs = $queryGetMessages->createCommand()->queryAll();
                if(count($msgs) > 0){
                    echo json_encode($msgs);
                }else{
                    echo json_encode([0, "Não existem mensagens"]);
                }
            }else{
                echo json_encode([0, "O usuário devem pertencer ao mesmo código de acesso a API"]);
            }
          }

        }
    }
    //
    public function actionGetMsgsFromReceptorId()
    {
        if(!(isset($_GET["codigo"]) && !empty($_GET["codigo"]))){
          echo json_encode([0, "Precisa Indicar o seu código"]);
        }else{
          if (!isset($_GET["receptor_id"])){
            echo json_encode([0, "Precisa Indicar o receptor_id"]);
          }else{
            $receptor = Usuario::find()->where(['id'=>$_GET["receptor_id"]])->One();
            if(ISSET($receptor) && $receptor->getPessoa()->One()->codigo == $_GET["codigo"]){
                $queryGetMessages = Mensagem::find()->where(['receptor_id'=>$receptor->id]);
                $msgs = $queryGetMessages->createCommand()->queryAll();
                if(count($msgs) > 0){
                    echo json_encode($msgs);
                }else{
                    echo json_encode([0, "Não existem mensagens"]);
                }
            }else{
                echo json_encode([0, "O usuário devem pertencer ao mesmo código de acesso a API"]);
            }
          }

        }
    }


}
