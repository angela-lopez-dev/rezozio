window.addEventListener('load',initState);
window.addEventListener('load',initListeners);

//Affiche la page en mode connecté ou déconnecté en fonction
//du contenu de data-user dans le code html.
function initState(ev){
  console.log("initialising state");
  let user = document.body.dataset.user;
  if(user)
    etatConnecte(JSON.parse(user));
  else
    etatDeconnecte();
}

//met en place les gestionnaires d'événements
function initListeners(ev){
document.forms.form_login.addEventListener('submit',login);
document.forms.form_signup.addEventListener('submit',signup);
document.querySelector('#logout').addEventListener('click',logout);
document.forms.search_bar.addEventListener('input',search);
}

//passage en mode connecté
function etatConnecte(user){
  for(let e of document.querySelectorAll('.deconnecte')){e.hidden = true;}
  for(let e of document.querySelectorAll('.connecte')){e.hidden = false;}
  removeFeed();//les messages précédents sont effacés.
  getFilteredFeed();
}

//passage en mode déconnecté
function etatDeconnecte(){
  for(let e of document.querySelectorAll('.deconnecte')){e.hidden = false;}
  for(let e of document.querySelectorAll('.connecte')){e.hidden = true;}
  removeFeed(); //les messages précédents sont effacés.
  getUnfilteredFeed();
}

function login(ev){
  console.log("login");
  ev.preventDefault(); //on empêche le formulaire d'aller vers la page login.php
  fetchFromJson("services/login.php",{method:'POST',body:new FormData(this),credentials:'same-origin'})
  .then(processLogin,errorLogin);
}

function processLogin(answer){
  if(answer.status =='ok')
    etatConnecte(answer.result);
  else
  document.forms.form_login.message.textContent = answer.message;
}

function errorLogin(error){
  document.forms.form_login.message.textContent = "Erreur, impossible de se connecter.";
}


function logout(ev){
  console.log("logout");
  fetchFromJson("services/logout.php",{method:'POST',credentials:'same-origin'})
  .then(processLogout,errorLogout);
}

function processLogout(answer){
  if(answer.status == 'ok')
    etatDeconnecte();
  else
    document.querySelector("#logout_error").textContent = answer.message;
}

function errorLogout(error){
  document.querySelector("#logout_error").textContent = "Impossible de se déconnecter, veuillez réessayer.";
}

function removeFeed(){
  document.querySelector('#messages').innerHTML="";
}
