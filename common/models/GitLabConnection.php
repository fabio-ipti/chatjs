<?php
namespace common\models;

use Yii;
use yii\base\NotSupportedException;

class GitLabConnection
{
    public const gitLabUrl = "http://glab.gitedu.com.br";
    const URL = "http://glab.gitedu.com.br/api/v3/";
    public const rootToken = "2PvhaoZqPY8ypqyzhrgY";

    public static function getClient($tokenUpdatedStatus = null){
        //Obtém o private_token do gitlab do usuário logado
        //As requisições ao gitlab serão autenticadas a partir desse token
        //Se não tem usuário logado, então é um convidado que solicitou e assim o toke se torna o do root
        $currentToken = ISSET(Yii::$app->user->identity) ?
          Yii::$app->user->identity->glab_private_token : self::rootToken;
        $token = ISSET($tokenUpdatedStatus) ? $tokenUpdatedStatus :  $currentToken;
        //Cria um nova conexão
        $autoload = Yii::$app->basePath."/../vendor/autoload.php";
        require_once $autoload;
        $client = new \Gitlab\Client(self::URL);
        $client->authenticate($token, \Gitlab\Client::AUTH_URL_TOKEN);
       return $client;
    }
}

?>
