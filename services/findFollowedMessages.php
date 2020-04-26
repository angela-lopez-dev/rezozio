<?php
/**args : before(défaut : vide),count(défaut : 15)
*result: tableau de {messageid,author,pseudo,content,datetime
*/
require_once('../lib/watchdog_service.php');
require_once('../lib/common_service.php');

$args = new RequestParameters();
$args->defineInt('before',['default'=>0,'min_range'=>0]);
$args->defineInt('count',['default'=>15,'min_range'=>0]);
$current = $_SESSION['id']->userId;
if(! $args->isValid()){
  produceError('Arguments invalides pour l\'accès au service.'.implode(', ',$args->getErrorMessages()));
  return;
}
try{
  $data = new DataLayer();
  $res = $data->findFollowedMessages($current,$args->before,$args->count);
  produceResult($res);
}catch(PDOException $e){
  produceError($e->getMessage());
}
 ?>
