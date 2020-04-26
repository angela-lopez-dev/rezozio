<?php
/**args : target
*result : true
*NB un utilisateur ne peut se follow lui même.*/
require("../lib/watchdog_service.php");
require_once('../lib/common_service.php');
$args = new RequestParameters();
$args->defineNonEmptyString('target');
$userId = $_SESSION['id']->userId;
if(!$args->isValid()){
  produceError('Arguments invalides pour l\'accès au service.'.implode(', ',$args->getErrorMessages()));
  return;
}
try{
  $data = new DataLayer();
  if(! $data->getUser($args->target)){
    produceError("Impossible de s'abonner à l'utilisateur : le compte n'existe pas.");
    return;
  }
  if($args->target==$userId){
    produceError("Impossible de s'abonner à l'utilisateur : le compte suiveur et suivi sont le même.");
    return;
  }
  $res = $data->follow($userId,$args->target);
  if(!$res)
    produceError("L'utilisateur demandé est déjà suivi, impossible de s'abonner une nouvelle fois.");
  else
    produceResult(true);
}catch(PDOException $e){
  produceError($e->getMessage());
}

 ?>
