<?php
require_once('../lib/watchdog_service.php');
require_once('../lib/common_service.php');
$current = $_SESSION['id']->userId;
$args = new RequestParameters();
$args->defineNonEmptyString('target');
if(! $args->isValid()){
  produceError('Arguments invalides pour l\'accès au service.'.implode(', ',$args->getErrorMessages()));
  return;
}
try{
  $data = new DataLayer();
  //vérifier si l'utilisateur est déjà bloqué (getUsers)
    //si oui => erreur
    //si non => bloquer
  $res = $data->blockUser($current,$args->target);
  if($res === false) //target n'existe pas ou current n'existe pas ou l'utilisateur est déjà bloqué
    produceError("Impossible de bloquer l'utilisateur.");
  else
    produceResult($res);
}catch(PDOException $e){
  produceError($e->getMessage());
}
?>
