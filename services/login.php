<?php
set_include_path('..'.PATH_SEPARATOR);

require_once('lib/common_service.php');
require_once('lib/session_start.php');


if ( ! isset($_SESSION['id'])) {
  $args = new RequestParameters('post');
  $args->defineNonEmptyString('login');
  $args->defineNonEmptyString('password');

  if (! $args->isValid()){
   produceError('argument(s) invalide(s) --> '.implode(', ',$args->getErrorMessages()));
   return;
  }
    $data = new DataLayer();
    $res = $data->authentifier($args->login,$args->password);
    if(!$res){
      produceError("Les identifiants sont incorrects.");
      return;
    }
    $_SESSION['id'] = $args->login;
    produceResult($args->login);

} else {
   produceError("L'utilisateur est déjà authentifié.");
   return;
}

 ?>
