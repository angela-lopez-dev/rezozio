<?php
/**
 *args : $messageId (obligatoire)
 *reponse : {messageId,author,pseudo,content,datetime} */
 require_once("../lib/common_service.php");
 $args = new RequestParameters();
 $args->defineInt('messageId');
 try {
   if(!$args->isValid()){
     produceError('Arguments invalides pour l\'accès au service.'.implode(', ',$args->getErrorMessages()));
     return;
   }
   $data = new DataLayer();
   $reponse = $data->getMessage($args->messageId);
   if($reponse)
    produceResult($reponse);
   else
    produceError('Le message demandé n\'existe pas.');
  }
  catch(PDOException $e)
 {
   produceError($e->getMessage());
 }
 ?>
