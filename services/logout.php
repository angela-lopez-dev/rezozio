<?php
/**MODE CONNECTE UNIQUEMENT
*args : aucun
*result : login de l'utilisateur déconnecté*/
set_include_path('..'.PATH_SEPARATOR);
require('../lib/watchdog_service.php');
$login = $_SESSION['id']->userId;
session_destroy();
produceResult($login);

 ?>
