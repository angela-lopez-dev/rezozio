<?php
/**args : source
*result : messageId*/
require_once('../lib/watchdog_service.php');
require_once('../lib/common_service.php');
const MAX_MESSAGE_LENGTH = 280;
$args = new RequestParameters('post');
$args->defineNonEmptyString('source');
$userId = $_SESSION['id']->userId;
if(!$args->isValid()){
  produceError('Arguments invalides pour l\'accès au service.'.implode(', ',$args->getErrorMessages()));
  return;
}
if(strlen($args->source)>MAX_MESSAGE_LENGTH){
  produceError('Impossible de poster le message, il doit comporter moins de 280 caractères.');
  return;
}
try{
  $data = new DataLayer();
  $res = $data->postMessage($args->source,$userId);
  if($res)
    produceResult($res['id']);
  else
    produceError("Impossible de poster le message.");

}catch(PDOException $e){
  produceError($e->getMessage());
}
 ?>
