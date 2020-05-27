<?php
/*Deletes a logged in user account */
require_once('../lib/watchdog_service.php');
require_once('../lib/common_service.php');
$current = $_SESSION['id']->userId;
try{
  $data = new DataLayer();
  $res = $data->deleteUser($current);
  if($res === false)
    produceError("Impossible de supprimer l'utilisateur.");
  else{
    require("logout.php");
  }
}catch(PDOException $e){
  produceError($e->getMessage());
}

?>
