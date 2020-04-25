<?php
/**
*args : userId(max 25 char),password,pseudo(max 25 char)
*result : userId,pseudo*/
  require_once('../lib/common_service.php');
  $args = new RequestParameters("post");
  $args->defineNonEmptyString('userId');
  $args->defineNonEmptyString('password');
  $args->defineNonEmptyString('pseudo');

  if(!$args->isValid()){
    produceError('Arguments invalides pour l\'accès au service.'.implode(', ',$args->getErrorMessages()));
    return;
  }
  if(! (strlen($args->userId) < 25 && strlen($args->pseudo) < 25)){
    produceError('Pseudo et/ou login trop long(s) : 24 caractères maximum.');
    return;
  }
  try{
    $data = new DataLayer();
    $res = $data->createUser($args->userId,$args->password,$args->pseudo);
    if($res)
      produceResult(["userId"=>$args->userId,"pseudo"=>$args->pseudo]);
    else
      produceError("Un utilisateur avec ce login existe déjà.");
  }catch(PDOException $e)
  {
    produceError($e->getMessage());
  }
 ?>
