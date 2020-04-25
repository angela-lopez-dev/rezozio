<?php
 require_once('common_service.php');
 require_once('session_start.php');

 if (isset($_SESSION['id']))
  return;

 produceError('non authentifié ');
 exit();
?>
