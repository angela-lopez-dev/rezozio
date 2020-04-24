<?php
 require_once('common_service.php');
 require_once('session_start.php');

 if (isset($_SESSION['ident']))
  return;

 produceError('non authentifié ');
 exit();
?>
