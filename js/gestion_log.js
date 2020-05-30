window.addEventListener('load',initState);
window.addEventListener('load',initListeners);

//Affiche la page en mode connecté ou déconnecté en fonction
//du contenu de data-user dans le code html.
function initState(ev){
  console.log("initialising state");
  let user = document.body.dataset.user;
  console.log("user : ");
  console.log(user);
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
document.forms.search_bar.addEventListener('submit',goToProfile);
document.querySelector("#unfiltered_feed").addEventListener('click',visitorMode);
document.querySelector("#filtered_feed").addEventListener('click',loggedInMode);
document.querySelector("#post_message").addEventListener('click',openMessageEditor);
document.forms.message_editor.addEventListener("submit",postMessage);
}

//passage en mode connecté
function etatConnecte(user){
  document.body.dataset.user=jsonUserToHTML(JSON.stringify(user));
  document.forms.form_login.reset();
  document.forms.form_signup.message.textContent="";
  document.querySelector("#menu_connecte").style.visibility="visible";
  for(let e of document.querySelectorAll('.deconnecte')){e.hidden = true;}
  for(let e of document.querySelectorAll('.connecte')){e.hidden = false;}
  removeFeed();//les messages précédents sont effacés.
  getFilteredFeed();
  showProfilePicture(user);
  updateButtons(modes.LOGGEDIN);
}

//passage en mode déconnecté
function etatDeconnecte(){
  for(let e of document.querySelectorAll('.deconnecte')){e.hidden = false;}
  for(let e of document.querySelectorAll('.connecte')){e.hidden = true;}
  document.body.removeAttribute('data-user');
  removeFeed(); //les messages précédents sont effacés.
  removeMenu();
  removeProfile();
  getFeed();
  closeEditProfile();
  updateButtons(modes.VISITOR);
}

function login(ev){
  console.log("login");
  ev.preventDefault(); //on empêche le formulaire d'aller vers la page login.php
  fetchFromJson("services/login.php",{method:'POST',body:new FormData(this),credentials:'same-origin'})
  .then(processLogin,errorLogin);
}

function processLogin(answer){
  console.log(answer);
  if(answer.status =='ok')
    UserObjectFromId(answer.result);

  else
  document.forms.form_login.message.textContent = answer.message;
}

function UserObjectFromId(userId){
  let data = new FormData();
  data.append("userId",userId);
  fetchFromJson("services/getUser.php",{method:'POST',body:data})
  .then(processUserFromId,errorUserFromId);
}

function processUserFromId(answer){
  console.log("creating user object from id ");
  console.log(answer);
  if(answer.status=="ok")
    etatConnecte(answer.result);
  else
      document.forms.form_login.message.textContent =answer.message;
}

function errorUserFromId(error){
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

function showProfilePicture(user){
  let img,pseudo,userId,profile;
  profile = document.querySelector("#profile");
  img = document.createElement('img');
  img.className='profile_picture';
  img.src='services/getAvatar.php?userId='+user.userId;
  img.alt='photo de profil';
  img.addEventListener('click',goToOwnProfile);

  pseudo = document.createElement('p');
  pseudo.textContent=user.pseudo;
  pseudo.className="menu_profile_pseudo";
  pseudo.addEventListener('click',goToOwnProfile);

  userId = document.createElement('p');
  userId.textContent="@"+user.userId
  userId.className="menu_profile_userId";
  userId.addEventListener('click',goToOwnProfile);


  profile.appendChild(img);
  profile.appendChild(pseudo);
  profile.appendChild(userId);
}

function jsonUserToHTML(json_user){
  const regexp = /""/gi;
  return json_user.replace(regexp,"&quot;");
}
