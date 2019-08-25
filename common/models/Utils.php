<?php
namespace common\models;

class Utils{


  //Converte a Data Postgres para o formato dd/mm/yyyy hh:mm:ss
  public static function postgresDataToDefault($datePg) {
    return date("d/m/Y H:i:s", strtotime($datePg));
  }

  //Retorna o intervalo de tempo das datas dadas
  public static function intervalTimePostgresData($startDatePg, $finishDatePg) {
    $startDate = new \DateTime($startDatePg);
    $finishDate = new \DateTime($finishDatePg);
    $interval = $finishDate->diff($startDate);
    return array('days'=>$interval->format('%a'), 'hours'=>$interval->format('%h:%i'));
  }


}

?>
