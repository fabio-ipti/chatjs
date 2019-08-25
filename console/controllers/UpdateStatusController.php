<?php
namespace console\controllers;

//require(__DIR__ . '/../../vendor/yiisoft/yii2/Yii.php');

use fedemotta\cronjob\models\CronJob;
use yii;
use yii\console\Controller;
use frontend\models\Assignment;
use frontend\models\Repository;
use frontend\models\TeamUser;
use frontend\models\User;
use common\models\GitLabConnection;
use yii\db\Query;
use common\models\Utils;

/**
 * UpdateStatusController controller
 */
class UpdateStatusController extends Controller {

    /**
     * Método Responsável por Atualizar automaticamente os Status das Tarefas
     */
    public function actionIndex(){
      //Definir timezone
      date_default_timezone_set('America/Maceio');

      //Desativar as tarefas
      $this->unactiveAssignments();
      //Ativar as tarefas
      $this->activeAssignments();

    }

    /**
     * Ativar automaticamente os Status das Tarefas
     * Para todas as tarefas que forem Ativadas deve-se transformar Todos os Alunos envolvidos
     * na tarefa em um 'Developer' nos repositórios que cada um faz parte relativos à tarefa
     */
    private function activeAssignments(){
      $currentDatePostGres = date("Y-m-d H:i:s", time());
      $assignmentToActive = Assignment::find()->where("active = false AND time_start <= '$currentDatePostGres' AND time_finish > '$currentDatePostGres'")->all();
      //Percorre cada ocorrência
      foreach($assignmentToActive AS $assignment){
        //1 - Ativar a tarefa no GitEdu;
        $assignment->active = true;
        $assignment->save();
        //2 - Altera os Alunos de 'Reporter' para 'Developer' nos repositórios do GitLab associado à tarefa
        //2.1 - Obter todos os reposítórios associados à tarefa corrente
        $allRepositories = Repository::find()->where(['assignment_fk' => $assignment->id])->all();
        //2.2 - Verificar se a tarefa é por Time ou Indivudual
        if($assignment->assignment_by_team){
          //2.3 - Percorre o array de repositórios e modifica os alunos para 'Developer' no GitLab
          foreach($allRepositories AS $repository){
            //2.3.1 - Encontra os alunos do Time do repositório corrente
            $allTeamUser = TeamUser::find()->where(['team_fk'=>$repository->team_fk])->all();
            foreach($allTeamUser AS $teamUser){
              $currentUser = $teamUser->userFk;
              if($currentUser->characterFk->name == \common\models\User::ROLE_STUDENT){
                //2.3.2 - Altera a permissão do aluno no GitLab para 'Developer'
                $this->saveProjectMember($repository->glab_project_fk, $currentUser->gitlab_user_fk, Repository::DEVELOPER_ACCESS);
              }
            }
          }
        }else{
          //2.3 - Percorre o array de repositórios e modifica os alunos para 'Developer' no GitLab
          foreach($allRepositories AS $repository){
            //2.3.1 - Já que é Individual, modifica o acesso para 'Reporter' do aluno associado ao repositório corrente
              $currentUser = $repository->userFk;
              if($currentUser->characterFk->name == \common\models\User::ROLE_STUDENT){
                //2.3.2 - Altera a permissão do aluno no GitLab para 'Developer'
                $this->saveProjectMember($repository->glab_project_fk, $currentUser->gitlab_user_fk, Repository::DEVELOPER_ACCESS);
              }
          }
        }
        //Agora enviar uma mensagem a cada usuário da turma que a tarefa foi Ativada
        //Envia um email para cada aluno e professor da turma a qual a tarefa foi criada
        $subject = "A Tarefa no GitEdu: ".$assignment->name. ", foi Ativada!";
        // Obtém todos os alunos e professores da Turma
        $queryAllUserInClassroom = (new Query())->select('u.id, p.name, p.email, u.gitlab_user_fk, u.ufs_enrollment')
        ->from('user AS u')
        ->join('INNER JOIN', 'person AS p', 'p.id = u.person_fk')
        ->join('INNER JOIN', 'user_classroom AS uc', 'uc.user_fk = u.id')
        ->join('INNER JOIN', 'character AS ch', 'ch.id = u.character_fk')
        ->where(['uc.classroom_fk'=>$assignment->classroom_fk]);
       $allUserInClassroom = $queryAllUserInClassroom->createCommand()->queryAll();
       foreach($allUserInClassroom AS $currentUser){
         $textBody = "A tarefa no GitEdu foi ativada na turma: ".$assignment->classroomFk->classroomModelFk->name."-".$assignment->classroomFk->year.".".$assignment->classroomFk->cyclo.
                      "<br><b> As submissões à tarefa estão liberadas. O prazo final de submissões está indicado abaixo. </b>".
                      "<br> Nome da Tarefa: ". $assignment->name.
                      "<br> Início: ".Utils::postgresDataToDefault($assignment->time_start)."
                       <br> Fim: ".Utils::postgresDataToDefault($assignment->time_finish)."
                       <br> Acesso em: <a href='gitedu.com.br/assignment/view?id=".$assignment->id."'>".$assignment->name."</a>
                       ";
         $this->sendEmail($currentUser["email"], $subject, $textBody);
        }

      }

    }

    /**
     * Desativar automaticamente os Status das Tarefas
     * Para todas as tarefas que forem Desativadas deve-se transformar Todos os Alunos envolvidos
     * na tarefa em um 'Reporter' nos repositórios que cada um faz parte relativos à tarefa
     */
    private function unactiveAssignments(){
      $currentDatePostGres = date("Y-m-d H:i:s", time());
      $assignmentToUnactive = Assignment::find()->where("active = true AND (time_finish < '$currentDatePostGres' OR time_start > '$currentDatePostGres')")->all();
      //Percorre cada ocorrência
      foreach($assignmentToUnactive AS $assignment){
        //1 - Desativa a tarefa no GitDcomp;
        $assignment->active = false;
        $assignment->save();
        //2 - Altera os Alunos de 'Developer' para 'Reporter' nos repositórios do GitLab associado à tarefa
        //2.1 - Obter todos os reposítórios associados à tarefa corrente
        $allRepositories = Repository::find()->where(['assignment_fk' => $assignment->id])->all();
        //2.2 - Verificar se a tarefa é por Time ou Indivudual
        if($assignment->assignment_by_team){
          //2.3 - Percorre o array de repositórios e modifica os alunos para 'Reporter' no GitLab
          foreach($allRepositories AS $repository){
            //2.3.1 - Encontra os alunos do Time do repositório corrente
            $allTeamUser = TeamUser::find()->where(['team_fk'=>$repository->team_fk])->all();
            foreach($allTeamUser AS $teamUser){
              $currentUser = $teamUser->userFk;
              if($currentUser->characterFk->name == \common\models\User::ROLE_STUDENT){
                //2.3.2 - Altera a permissão do aluno no GitLab para 'Reporter'
                $this->saveProjectMember($repository->glab_project_fk, $currentUser->gitlab_user_fk, Repository::REPORTER_ACCESS);
              }
            }
          }
        }else{
          //2.3 - Percorre o array de repositórios e modifica os alunos para 'Reporter' no GitLab
          foreach($allRepositories AS $repository){
            //2.3.1 - Já que é Individual, modifica o acesso para 'Reporter' do aluno associado ao repositório corrente
              $currentUser = $repository->userFk;
              if($currentUser->characterFk->name == \common\models\User::ROLE_STUDENT){
                //2.3.2 - Altera a permissão do aluno no GitLab para 'Reporter'
                $this->saveProjectMember($repository->glab_project_fk, $currentUser->gitlab_user_fk, Repository::REPORTER_ACCESS);
              }
          }
        }

        //Agora enviar uma mensagem a cada usuário da turma que a tarefa foi Desativada
        //Envia um email para cada aluno e professor da turma a qual a tarefa foi Desativada
        $subject = "A Tarefa no GitEdu: ".$assignment->name. ", foi Desativada!";
        // Obtém todos os alunos e professores da Turma
        $queryAllUserInClassroom = (new Query())->select('u.id, p.name, p.email, u.gitlab_user_fk, u.ufs_enrollment')
        ->from('user AS u')
        ->join('INNER JOIN', 'person AS p', 'p.id = u.person_fk')
        ->join('INNER JOIN', 'user_classroom AS uc', 'uc.user_fk = u.id')
        ->join('INNER JOIN', 'character AS ch', 'ch.id = u.character_fk')
        ->where(['uc.classroom_fk'=>$assignment->classroom_fk]);
       $allUserInClassroom = $queryAllUserInClassroom->createCommand()->queryAll();
       foreach($allUserInClassroom AS $currentUser){
         $textBody = "A tarefa no GitEdu foi desativada na turma: ".$assignment->classroomFk->classroomModelFk->name."-".$assignment->classroomFk->year.".".$assignment->classroomFk->cyclo.
                      "<br><b> Novas submissões à tarefa estão bloqueadas. </b>".
                      "<br> Nome da Tarefa: ". $assignment->name.
                      "<br> Início: ".Utils::postgresDataToDefault($assignment->time_start)."
                       <br> Fim: ".Utils::postgresDataToDefault($assignment->time_finish)."
                       <br> Acesso em: <a href='gitedu.com.br/assignment/view?id=".$assignment->id."'>".$assignment->name."</a>
                       ";
         $this->sendEmail($currentUser["email"], $subject, $textBody);
        }
      }
    }

    private function sendEmail($email, $subject, $textBody){
      if(ISSET($email, $subject, $textBody)){
        //'embed-email', ['imageFileName' => 'http://gitedu.com.br/images/logos/full-color.svg']
       $message = Yii::$app->mailer->compose();
       $head = "<img style='height: 60px;' src='".$message->embed('http://gitedu.com.br/images/logos/full-color.svg')."'>
           <br><br>";
       $message->setFrom('contato@gitedu.com.br');
       $message->setTo($email);
       $message->setSubject($subject);
       $message->setTextBody("");
       $message->setHtmlBody($head.$textBody);
       $message->send();
     }
    }

    //Métodos para o GitLab
    private function saveProjectMember($project_id, $user_id, $access_level){
      //COLOCAR UM TOKEN PADRÃO FIXO DO meu usuário ROOT
      $tokenUpdatedStatus = "2PvhaoZqPY8ypqyzhrgY";
      $client = GitLabConnection::getClient($tokenUpdatedStatus);
      return $client->api('projects')->saveMember($project_id, $user_id, $access_level)['id'];
    }
    //==============================================

}
?>
