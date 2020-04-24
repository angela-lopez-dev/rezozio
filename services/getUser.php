<?php
  /**
  *args : $userId
  *reponse : {pseudo,userId}
  */
  require_once("../lib/common_service.php");
  $args = new RequestParameters();
  $args->defineNonEmptyString('userId');
try{
  if(!$args->isValid()){
    produceError('Arguments invalides pour l\'accès au service.'.implode(', ',$args->getErrorMessages()));
    return;
  }
  $data = new DataLayer();
  $reponse = $data->getUser($args->userId);
  if($reponse){
    $reponse['userId'] = $args->userId;
    produceResult($reponse);
  }
  else
    produceError('L\'utilisateur demandé n\'existe pas.');
}
catch(PDOException $e){
  produceError($e->getMessage());
}
 ?>
