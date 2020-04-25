<?php
/**
*args searchedString (>3 characters)
*result : tableau de [{userId,pseudo}]
*/
const MIN_SEARCHED_STRING = 3;
require_once('../lib/DataLayer.class.php');
require_once('../lib/common_service.php');
$args = new RequestParameters();
$args->defineNonEmptyString('searchedString');
if(! $args->isValid()){
  produceError('Arguments invalides pour l\'accès au service.'.implode(', ',$args->getErrorMessages()));
  return;
}

if(strlen($args->searchedString) <MIN_SEARCHED_STRING){
  produceError("Chaîne de recherche trop courte : au moins 3 caractères.");
  return;
}

try{
  $data = new DataLayer();
  $res = $data->findUsers($args->searchedString);
  produceResult($res);
}catch(PDOException $e){
  produceError($e->getMessage());
}
?>
