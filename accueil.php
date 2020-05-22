<?php
require("lib/Identite.class.php");
require("lib/session_start.php");
if(isset($_SESSION['id']))
  $user = $_SESSION['id'];
require("views/pageAccueil.php");
?>
