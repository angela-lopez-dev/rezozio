<?php
/*Attend la variable globale :
* -$user (si l'utilisateur est connecté)
* qui est un objet {login,pseudo}*/
require_once("lib/Identite.class.php");
$dataUser ="";
if(isset($user))
  $dataUser ='data-user="'.htmlentities(json_encode($user)).'"';
?>
<!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
 <meta charset="UTF-8" />
 <title>Rezozio</title>
 <link rel ="stylesheet" type="text/css" href="style/style.css">
 <script src="js/fetchUtils.js"></script>
 <script src="js/feed.js"></script>
 <script src ="js/gestion_log.js"></script>
 <script src="js/signup.js"></script>
 <script src="js/search.js"></script>

</head>
<?php
  echo "<body $dataUser>";
?>
  <section>
    <form action ="" method=POST id="search_bar">
      <input type ="text" name="searchedString" placeholder="rechercher"/></br>
      <button type="submit" name="valid">OK</button></br>
    </form>
    <div id="search_results">  </div>
    <div id="search_results_error">  </div>
  </section>

  <section class ="deconnecte">
    <form method="POST" action="" id ="form_login">
      <fieldset>
        <legend>Se connecter</legend>
        <input type="text" name="login" id="login" placeholder="login" required/></br>
        <input type="password" name="password" id="password"  placeholder="password" required/></br>
        <button type="submit" name="valid">OK</button></br>
        <output for="login password" name="message"></output>
      </fieldset>
    </form>
    <form method="POST" action="" id="form_signup">
      <fieldset>
        <legend>Créer un compte</legend>
        <input type="text" name="userId" id="login" placeholder="login" required/></br>
        <input type="text" name="pseudo" id="pseudo" placeholder="pseudo"required/></br>
        <input type="password" name="password" id="password"  placeholder="password"required/></br>
        <button type="submit" name="valid">OK</button></br>
        <output for="login password pseudo" name="message"></output>
      </form>

  </section>

  <section class ="connecte">
    <p>Vous êtes connecté ! </p>
    <button id="logout" name="logout">Se déconnecter</button></br>
    <div id ="logout_error">
    </div>
  </section>
  <section class ="stable">
  <div id="messages">
  </div>
</section>

</body>
</html>
