<?php
namespace common\components;

use yii\db\Query;
use frontend\models\Assignment;
use frontend\models\Team;

class Helper {

  //@return String without Accent
  public static function removeAccentFromUtf8Str($str){
    // assume $str esteja em UTF-8
    $from = "áàãâéêíóôõúüçÁÀÃÂÉÊÍÓÔÕÚÜÇ";
    $to = "aaaaeeiooouucAAAAEEIOOOUUC";
    $keys = array();
    $values = array();
    preg_match_all('/./u', $from, $keys);
    preg_match_all('/./u', $to, $values);
    $mapping = array_combine($keys[0], $values[0]);
    return strtr($str, $mapping);
  }

  //@return Object
  public static function getClassroomByGLabPath($school_fk, $classroomName, $classroomYear, $classroomCyclo){
    //Verifica se já existe um Path de grupo do GitLab, igual ao gerado por name+year+cyclo passado
    $repetedClassroom = null;
    if(ISSET($school_fk) && ISSET($classroomName) && ISSET($classroomYear) && ISSET($classroomCyclo) &&
        !empty($school_fk) && !empty($classroomName) && !empty($classroomYear) && !empty($classroomCyclo)){
      $nameYearCycloPassed = $classroomName."-".$classroomYear.".".$classroomCyclo;
      $defaultPath = Helper::buildValidGlabGroupPath($school_fk."-".$nameYearCycloPassed);
      $queryClassroomsByPeriod = (new Query())->select('c.id, cm.name, c.year, c.cyclo')
      ->from('classroom AS c')
      ->join('INNER JOIN','classroom_model AS cm', 'c.classroom_model_fk = cm.id')
      ->where(['c.school_fk' => $school_fk, 'c.year'=>$classroomYear, 'c.cyclo'=>$classroomCyclo]);
      $resultsClassroomsByPeriod = $queryClassroomsByPeriod->createCommand()->queryAll();
      if(count($resultsClassroomsByPeriod) > 0){
        //Percorrer cada turma que possui mesmo periodo
        //E gerar o Path a partir do name + year + cyclo e comparar com o Path a partir do name + year + cyclo Passado
        foreach($resultsClassroomsByPeriod AS $classroom){
          $currentNameYearCyclo = $classroom['name']."-".$classroom['year'].".".$classroom['cyclo'];
          if(Helper::buildValidGlabGroupPath($school_fk."-".$currentNameYearCyclo)
              == $defaultPath){
                //Encontrou uma Classe que possui o mesmo GitLab Path
                $repetedClassroom = $classroom;
                break;
              }
        }

      }
    }

    return $repetedClassroom;
  }

  //@return Object
  public static function getAssignmentByGLabNameAndClassroom($assignmentName, $classroomID){
    //Verifica se já existe um nome de projeto do GitLab igual ao gerado pelo nome passado
    //Que faça parte de uma mesma Turma
    $repetedAssignment = null;
    if(ISSET($classroomID) && ISSET($assignmentName) &&
        !empty($classroomID) && !empty($assignmentName)){
          $defaultProjectName = Helper::buildValidGlabProjectName($assignmentName);
          $allAssignmentByClassroom = Assignment::find()->where(['classroom_fk'=>$classroomID])->all();
          foreach($allAssignmentByClassroom AS $assignment){
              if( Helper::buildValidGlabProjectName($assignment->name) == $defaultProjectName){
                //Encontrou uma tarefa dentro da mesma classe que possui o mesmo 'nome de projeto no GitLab'
                $repetedAssignment = $assignment;
                break;
              }
          }
      }

      return $repetedAssignment;
  }

  //@return Object
  public static function getTeamByNameAndClassroom($teamName, $classroomID){
    //Verifica se já existe um nome do time
    //Que faça parte de uma mesma Turma
    $repetedTeam = null;
    if(ISSET($classroomID) && ISSET($teamName) &&
        !empty($classroomID) && !empty($teamName)){
          $teamByNameAndClassroom = Team::find()->where(['classroom_fk'=>$classroomID, 'name'=>$teamName])->one();
          if(ISSET($teamByNameAndClassroom)){
            $repetedTeam = $teamByNameAndClassroom;
          }
      }

      return $repetedTeam;
  }



  //Função para montar o Caminho do Grupo no GitLab a patir do name + year + cyclo da Turma
  //@return String
  public static function buildValidGlabGroupPath($glabGroupName){
    //Path pode conter somente letras, dígitos, '_', '-' e '.'. Não pode iniciar com '-' ou terminar em '.'.
    // 1 - Substituir espaço por -
    $path = preg_replace('/( )+/', '-', $glabGroupName);
    // 2 - Retirar, se existir o '-' no início
    $path = preg_replace('/^-+/', '', $path);
    // 3 - Retirar, se existir o '.' no final
    $path = preg_replace('/\.+$/', '', $path);
    // 4 - Retirar acentuação
    //echo mb_detect_encoding($path);
    $path = Helper::removeAccentFromUtf8Str($path);
    //5 - Eliminar os caracteres especiais restantes
     preg_match_all('/\w|\d|\.|\-|\_/', $path, $path);
     $path = implode("", $path[0]);
     return $path;
  }


  //Função para montar o Nome do projeto no GitLab a partir do nome da tarefa
  //@return String
  public static function buildValidGlabProjectName($assignmentName){
    //"name" pode conter somente letras, dígitos, '_', '.', '-' and espaço. Deve iniciar com letra, dígito ou '_'.
    // 1 - Retirar, se existir o '-' no início
    $name = preg_replace('/^-+/', '', $assignmentName);
    // 2 - Retirar, se existir o '.' no início
    $name = preg_replace('/^\.+/', '#', $name);
    // 3 - Retirar, se existir o '.' no final
    $name = preg_replace('/\.+$/', '', $name);
    // 4 - Retirar acentuação
    $name = Helper::removeAccentFromUtf8Str($name);
    //5 - Eliminar os caracteres especiais restantes
    preg_match_all('/\w|\d|\.|\-|\_|( )/', $name, $name);
    $name = implode("", $name[0]);
    return $name;
  }

  // Função que gera e retorna a quantidade solicitada de caracteres alfa-numéricos aleatoriamente
  public static function getRandonAlphaNumericCharactere($numCharacters){
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $numCharacters; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
  }

}


?>
