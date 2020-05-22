<?php
/**args : userId,size(optionnelle)
*result : avatar ou erreur
*/
  require_once("../lib/common_service.php");
  require_once("../lib/RequestParameters.class.php");
  $args = new RequestParameters();
  $args->defineString("size",["default"=>"small"]);
  $args->defineNonEmptyString("userId");
  if(! $args->isValid()){
    produceError("Arguments invalides pour ce service : ".implode(', ',$args->getErrorMessages()));
    return;
  }
  if($args->size != "small" && $args->size != "large"){
    produceError("Arguments invalides pour ce service: size doit être large ou small.");
    return;
  }
  try{
    $data = new DataLayer();
    $imgSpec = $data->getAvatar($args->userId,$args->size);
    if(!$imgSpec){
      produceError("Impossible de récupérer l'avatar. L'utilisateur n'existe pas.");
      return;
    }
      header("Content-Type: ".$imgSpec['mimetype']);
      $flux = $imgSpec['data'];
      if(is_null($flux)){ //l'utilisateur n'a pas d'avatar défini
        if($args->size === "small")
          $flux = fopen('../images/default_small.png','r');
        else
          $flux = fopen('../images/default_large.png','r');
      }
      fpassthru($flux);
      fclose($flux);
  }catch(PDOException $e)
  {
    produceError($e->getMessage());
  }
exit();

?>
