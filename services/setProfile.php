<?php
require_once('../lib/watchdog_service.php');
require_once('../lib/common_service.php');
$args = new RequestParameters('post');
$args->defineString('password',['default'=>'']);
$args->defineString('pseudo',['default'=>'']);
$args->defineString('description',['default'=>'']);

$user = $_SESSION['id'];
if(!$args->isValid()){
  produceError('Arguments invalides pour l\'accès au service.'.implode(', ',$args->getErrorMessages()));
  return;
}
if($args->password =='' && $args->pseudo =='' && $args->description == ''){
  //aucun changement
  produceResult("pas de paramètres");
  return;
}
try{
$data = new DataLayer();
$res = $data->setProfile($user->userId,$args->pseudo,$args->description,$args->password);
if($res !== false)
  produceResult($user);
else
  //cas improbable où l'utilisateur supprime son compte et met à jour son profil en même temps.
  produceError("L'utilisateur n'existe pas.");
}catch(PDOException $e){
  produceError($e->getMessage());
}
?>
