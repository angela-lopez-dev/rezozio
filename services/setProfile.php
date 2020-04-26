<?php
require_once('../lib/watchdog_service.php');
require_once('../lib/common_service.php');
$args = new RequestParameters('post');
$args->defineString('password',['default'=>'']);
$args->defineString('pseudo',['default'=>'']);
$args->defineString('description',['default'=>'']);

$userId = $_SESSION['id'];
if(!$args->isValid()){
  produceError('Arguments invalides pour l\'accès au service.'.implode(', ',$args->getErrorMessages()));
  return;
}
if($args->password =='' && $args->password)
?>
