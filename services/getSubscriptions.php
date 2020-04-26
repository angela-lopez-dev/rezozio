<?php
require_once('../lib/watchdog_service.php');
require_once('../lib/common_service.php');
$current = $_SESSION['id']->userId;
try{
  $data = new DataLayer();
  $res = $data->getSubscriptions($current);
  produceResult($res);
}catch(PDOException $e){
  produceError($e->getMessage());
}
?>
