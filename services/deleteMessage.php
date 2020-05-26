<?php
/**args : messageId
*result : messageId*/
require_once('../lib/watchdog_service.php');
require_once('../lib/common_service.php');
$args = new RequestParameters();
$args->defineInt('messageId');
$userId = $_SESSION['id']->userId;
if(!$args->isValid()){
  produceError('Arguments invalides pour l\'accès au service.'.implode(', ',$args->getErrorMessages()));
  return;
}
try{
  $data = new DataLayer();
  $res = $data->deleteMessage($args->messageId,$userId);
  if($res !== false)
    produceResult($res);
  else
    produceError("Impossible de supprimer le message, vous n'en n'êtes pas l'auteur ou bien il n'existe pas.");

}catch(PDOException $e){
  produceError($e->getMessage());
}
 ?>
