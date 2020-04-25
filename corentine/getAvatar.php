<?php
  /**
  *args : userId(obligatoire),size(optionnel, default = 'small')
  *reponse : l'avatar de l'utilisateur en cas de succès ou un
  *message d'erreur json*/
  require_once("../lib/common_service.php");

  $args = new RequestParameters();
  $args->defineNonEmptyString('userId');
  $args->defineString('size',['default'=>'small']);

  if($args->size != 'small' and $args->size != 'large'){
    produceError('Argument invalide pour ce service : size doit être small ou large.');
    return;
  }
  try{
  $data = new DataLayer();
  $imgSpec = $data->getAvatar($args->userId,$args->size);
  if(!$imgSpec){
    produceError('Impossible de récupérer l\'avatar, cet utilisateur n\'existe pas.');
    return;
  }
  $flux = $imgSpec['data'];
  header("Content-Type: ".$imgSpec['mimetype']);
  if(is_null($flux)) //l'utilisateur n'a pas d'avatar défini
    $flux = fopen('../images/default.jpg','r');
  fpassthru($flux);
  exit();
  }catch(PDOException $e){
    produceError($e->getMessage());
  }
?>
