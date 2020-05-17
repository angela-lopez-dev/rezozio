<?php
/**
 *args : $messageId (obligatoire)
 *reponse : {messageId,author,pseudo,content,datetime} */
 require_once("../lib/common_service.php");
 require_once("../lib/session_start.php");
 $args = new RequestParameters();
 $args->defineInt('messageId');
 $current = $_SESSION['id']->userId;
 try {
   if(!$args->isValid()){
     produceError('Arguments invalides pour l\'accès au service.'.implode(', ',$args->getErrorMessages()));
     return;
   }
   $data = new DataLayer();
   $reponse = $data->getMessage($args->messageId,$current);
   if($reponse)
    produceResult($reponse);
   else
    produceError('Le message demandé n\'existe pas ou bien son auteur vous a bloqué.');
  }
  catch(PDOException $e)
 {
   produceError($e->getMessage());
 }
 ?>
