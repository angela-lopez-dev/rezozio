<?php
  /**
  *args : $userId (obligatoire)
  *reponse : {userId,pseudo,description,*followed,*isFollower} *optionnels
  */
  require_once("../lib/common_service.php");
  require_once("../lib/watchdog_service.php");
  //le service fonctionne en mode connecte ou non connecte mais il opère différement => pas de watchdog.
  $current = $_SESSION['id']->userId;
  $args = new RequestParameters();
  $args->defineNonEmptyString('userId');

try{
  if(!$args->isValid()){
    produceError('Arguments invalides pour l\'accès au service.'.implode(', ',$args->getErrorMessages()));
    return;
  }
  $data = new DataLayer();
  $reponse = $data->getProfile($args->userId,$current);
  if($reponse)
    produceResult($reponse);
  else
    produceError('L\'utilisateur demandé n\'existe pas.');

}
catch(PDOException $e){
  produceError($e->getMessage());
}
 ?>
