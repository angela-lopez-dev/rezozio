<?php
 date_default_timezone_set ('Europe/Paris');
 header('Content-type: application/json; charset=UTF-8');
 require_once(__DIR__."/RequestParameters.class.php");
 require_once(__DIR__."/DataLayer.class.php");
/*attend une variable globale $args (les arguments d'appel au service)*/
 function answer($reponse){
  global $args;
  if (is_null($args))
    $reponse['args'] = [];
  else {
    $reponse['args'] = $args->getValues();
    unset($reponse['args']['password']);
  }
  echo json_encode($reponse);
 }

 function produceError($message){
    answer(['status'=>'error','message'=>$message]);
 }
 function produceResult($result){
    answer(['status'=>'ok','result'=>$result]);
 }
/***** ***************IMG ******************/
 function produceImgResult($result){
   imgAnswer(["status"=>"ok","result"=>$result]);
 }

 function produceImgError($message){
   imgAnswer(["status"=>"error","message"=>$message]);
 }

 function imgAnswer($reponse){
   global $_FILES;
   if(is_null($_FILES))
     $reponse['args'] = [];
   else {
   $reponse['args'] = $_FILES['image'];
   unset($reponse['args']['tmp_name']);
   }
   echo json_encode($reponse);
 }

?>
