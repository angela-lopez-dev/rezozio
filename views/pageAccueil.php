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
 <script src="js/profile.js"></script>
 <script src="js/menu.js"></script>
 <script src="js/message.js"></script>

</head>
<?php
  echo "<body $dataUser>";
?>
  <div id="home"></div>

  <div class ="connecte" id="profile">
  </div>
  <section>
    <form action ="" method=POST id="search_bar" autocomplete="off">
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
        <input type="text" name="userId" id="login" placeholder="login" maxlength="25" required/></br>
        <input type="text" name="pseudo" id="pseudo" placeholder="pseudo" maxlegth="25" required/></br>
        <input type="password" name="password" id="password" placeholder="password" maxlength="25" required/></br>
        <button type="submit" name="valid">OK</button></br>
        <output for="login password pseudo" name="message"></output>
      </form>

  </section>

  <section class ="connecte">
    <button id="logout" name="logout">se déconnecter</button></br>
    <div id ="logout_error">
    </div>
    <button id="unfiltered_feed">tous les messages</button></br>
    <button id="filtered_feed" hidden=true>mes abonnements </button></br>
    <button id="post_message">Publier un message </button></br>
    <div id="message_editor_container" hidden=true>
      <span class="close">&times;</span>
      <form action="" method="POST" id="message_editor">
        <textarea id="message_content" name="source"
          rows="4" cols="70" maxlength="280" required>
</textarea>
<button type="submit" name="valid">Publier</button>
<output name="output"></output>
      </form>
    </div>
  </section>
  <section class ="stable">
    <section>
      <div id="userProfile"></div>
    </section>
    <div id="profile_editor_container" hidden=true>
      <span class="close">&times;</span>
      <form action="" method="POST" id="profile_editor">
        <legend>Modifier le profil</legend>
        <label for="form_pp">Photo de profil</label>
        <input type="file" name="image" id="form_pp"/></br>
        <input type="text" name="pseudo" placeholder="pseudo"id="form_pseudo" maxlength="25"/></br>
        <textarea id="form_description" name="description"
          rows="5" cols="33" maxlength="1024">
description
</textarea>

        <input type="password" name="password" placeholder="password" id="form_password"></br>
        <button type="submit" name="valid">Valider les changements</button>
        <output for ="image pseudo description password" name="output"></output>
      </form>
    </div>

  <div id="messages">
  </div>
</section>

</body>
</html>
