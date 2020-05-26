//****** CONSULTER UN PROFIL D'UTILISATEUR*********//

//cas du formulaire de recherche
function goToProfile(ev){
  ev.preventDefault();
  visitProfile(this.searchedString.value);
}


//cas général (lien des messages, listes etc..)
function visitProfile(userId){
  refreshProfile(userId);
  removeFeed();
  getFeedByAuthor(userId);
}
function refreshProfile(userId){
  let data = new FormData();
  data.append("userId",userId);
  fetchFromJson("services/getProfile.php",{method:'POST',body:data,credentials:'same-origin'})
  .then(processGoToProfile,errorGoToProfile);
}
function processGoToProfile(answer){
  removeProfile();
  if(answer.status=='ok')
    setupProfile(answer.result,document.querySelector("#userProfile"));
  else
    displayError(answer.error,document.querySelector("#userProfile"));
}

function errorGoToProfile(error){
  displayError(error,document.querySelector("#userProfile"));
}

//contourne le problème de cache qui empêche
//d'actualiser la photo de profil après modification
//sans refresh la page.
function generateUniqueImgUrl(size,userId){
let today = new Date();
let date = today.getFullYear()+'_'+(today.getMonth()+1)+'_'+today.getDate();
let time = today.getHours() + "_" + today.getMinutes() + "_" + today.getSeconds();
let dateTime = date+'_'+time;

let min,max,r;
min = 0;
max = 120;
min = Math.ceil(min);
max=Math.floor(max);
r =Math.floor(Math.random()*(max-min)) +min;
return("services/getAvatar.php?size="+size+"&userId="+userId+"&r="+dateTime+r.toString());

}
 function setupProfile(profile,element){
   let userId,pseudo,description,subscriptions,isFollower,block,home,img,edit,getFollowers,getFollows;

   userId = document.createElement("p");
   userId.className="profile_userId";
   userId.textContent="@"+profile.userId;

   pseudo = document.createElement("p");
   pseudo.className="profile_pseudo";
   pseudo.textContent=profile.pseudo

   description = document.createElement("p");
   description.className="profile_description";
   description.textContent=profile.description;

   img = document.createElement('img');
   img.className='profile_picture';
   generateUniqueImgUrl("large",profile.userId);
   img.src=generateUniqueImgUrl("large",profile.userId);
   img.alt='photo de profil';

  element.appendChild(img);
  element.appendChild(pseudo);
  element.appendChild(userId);
  element.appendChild(description);
//mode connecté et le profil visité n'est pas le sien
  if(profile.hasOwnProperty("followed") && JSON.parse(document.body.dataset.user).userId !== profile.userId){
  subscriptions=document.createElement("button");
  subscriptions.className="subscriptions_button";
  if(!profile.followed){
   subscriptions.textContent="s'abonner";
   subscriptions.addEventListener("click",follow);
 }
 else{
   subscriptions.textContent="se désabonner";
   subscriptions.addEventListener("click",unfollow);
 }


 if(profile.isFollower){
   isFollower = document.createElement("p");
   isFollower.id = "profile_isFollower";
   isFollower.textContent="est abonné à votre compte.";
   element.appendChild(isFollower);
 }
 element.appendChild(subscriptions);
 block = document.createElement("button");
 block.id="profile_block";
 if(!profile.blocked){
   block.textContent="bloquer";
   block.addEventListener("click",blockUser);
 }
 else{
   block.textContent="débloquer";
   block.addEventListener("click",unblockUser);
 }
 element.appendChild(block);
 }

//mode connecté et le profil visité est le sien
 else if(JSON.parse(document.body.dataset.user).userId == profile.userId){
  //possibilité de modifier son profil
   edit = document.createElement("button");
   edit.id="edit_profile";
   edit.textContent="Modifier le profil";
   edit.addEventListener("click",openProfileEditing);
   element.appendChild(edit);
  //afficher sa liste de followers
  let followers_c = document.createElement("div");
  followers_c.id="followers_c";
  getFollowers = document.createElement("button");
  getFollowers.className="profile_button";
  getFollowers.textContent="abonnés";
  getFollowers.addEventListener("click",openFollowersList);
  followers_c.appendChild(getFollowers);
  element.appendChild(followers_c);

  //afficher sa liste d'abonnements
  let follows_c = document.createElement("div");
  follows_c.id="follows_c";
  getFollows= document.createElement("button");
  getFollows.className="profile_button";
  getFollows.textContent="abonnements";
  getFollows.addEventListener("click",openFollowsList);
  follows_c.appendChild(getFollows);
  element.appendChild(follows_c);

 }
 //bouton home
   home = document.createElement("button");
   home.textContent="Accueil";
   home.id="home_button";
   home.addEventListener('click',goHome);
  document.querySelector("#home").appendChild(home);


 }

function follow(){
  let data = new FormData();
  data.append("target",document.querySelector(".profile_userId").textContent.substring(1));
  fetchFromJson("services/follow.php",{method:'POST',body:data,credentials:'same-origin'})
  .then(processToggleSubscribe,displayProfileError);
}

function unfollow(){
  let data = new FormData();
  data.append("target",document.querySelector(".profile_userId").textContent.substring(1));
  fetchFromJson("services/unfollow.php",{method:'POST',body:data,credentials:'same-origin'})
  .then(processToggleSubscribe,displayProfileError);
}

function processToggleSubscribe(answer){
  if(answer.status == "ok")
    refreshProfile(document.querySelector(".profile_userId").textContent.substring(1));
  else
    displayProfileError(answer.message);
}

function displayProfileError(error){
  d = document.createElement("div");
  d.className="subscribe_error";
  p = document.createElement("p");
  p.textContent=error;
  d.appendChild(p);
  document.querySelector('#userProfile').appendChild(d);
}
function removeProfile(){
  document.querySelector("#userProfile").innerHTML="";
  document.querySelector("#home").innerHTML="";
}

function blockUser(ev){
  let data = new FormData();
  data.append("target",document.querySelector(".profile_userId").textContent.substring(1));
  fetchFromJson("services/blockUser.php",{method:'POST',body:data,credentials:'same-origin'})
  .then(processBlock,displayProfileError);
}

function processBlock(answer){
  if(answer.status == "ok")
    refreshProfile(document.querySelector(".profile_userId").textContent.substring(1))
  else
    displayProfileError(answer.message);
}


function unblockUser(ev){
  let data = new FormData();
  data.append("target",document.querySelector(".profile_userId").textContent.substring(1));
  fetchFromJson("services/unblockUser.php",{method:'POST',body:data,credentials:'same-origin'})
  .then(processBlock,displayProfileError);
}


//**CONSULTER SON PROPRE PROFIL **//


function goToOwnProfile(){
  refreshProfile(JSON.parse(document.body.dataset.user).userId);
  removeFeed();
  getFeedByAuthor(JSON.parse(document.body.dataset.user).userId);
}
//ouvre un pop-up permettant de modifier
//son profil.
function openProfileEditing(){
  //removeProfile();
  document.querySelector("#profile_editor_container").hidden=false;
  document.querySelector("#profile_editor").addEventListener('submit',editProfile);
}

function editProfile(ev){
  ev.preventDefault();
  let data = new FormData(this);
  if(data.get("image"))
    fetchFromJson("services/uploadAvatar.php",{method:'POST',body:data,credentials:'same-origin'})
    .then(processEditProfile,errorEditProfile);
  fetchFromJson("services/setProfile.php",{method:'POST',body:data,credentials:'same-origin'})
  .then(processEditProfile,errorEditProfile);
}

function processEditProfile(answer){
  if(answer.status=="ok"){
    document.querySelector("#profile_editor").output.textContent="Changements validés.";
    refreshProfile(document.querySelector(".profile_userId").textContent.substring(1));
    removeFeed();
    getFeedByAuthor(document.querySelector(".profile_userId").textContent.substring(1));
  }
  else
    document.querySelector("#profile_editor").output.textContent="Impossible de valider les changement : "+answer.message;
}
function errorEditProfile(error){
  document.querySelector("#profile_editor").output.textContent="Impossible de valider les changement : "+error;
}

function openFollowersList(){
  fetchFromJson("services/getFollowers.php",{method:'POST',credentials:'same-origin'})
  .then(processFollowersList,errorFollowersList);
}

function openFollowsList(){
  fetchFromJson("services/getSubscriptions.php",{method:'POST',credentials:'same-origin'})
  .then(processFollowsList,errorFollowersList);
}

function processFollowersList(answer){
  if(answer.status=="ok")
    setupFollowersList(answer.result);
  else
    errorFollowersList(answer.message);
}

function processFollowsList(answer){
  if(answer.status=="ok")
    setupFollowsList(answer.result);
  else
    errorFollowsList(answer.message);
}
function errorFollowersList(error){
  displayError(error,document.querySelector("#userProfile"));
}

function setupFollowsList(result){
  closeFollowsList();
  console.log("follows list");
  let list = document.createElement("div");
  list.id="follows_list";
  let s = document.createElement("span");
  s.textContent="\u00D7";
  s.addEventListener("click",closeFollowsList);
  list.appendChild(s);


  for(let i =0;i<result.length;i++){
    let d = document.createElement("div");
    d.className="f_list_profile";
    let pseudo = document.createElement("p");
    pseudo.textContent=result[i].pseudo;

    let userId = document.createElement("p");
    userId.textContent="@"+result[i].userId;

    let b = document.createElement("button");
    b.className="f_list_button";
    b.textContent="se désabonner";
     b.addEventListener("click",function(){unfollowFromList(result[i].userId);});


    pseudo.addEventListener("click",function(){visitProfile(result[i].userId);});
    userId.addEventListener("click",function(){visitProfile(result[i].userId);});

    b.addEventListener("click",debugEvent);

    pseudo.addEventListener("click",debugEvent);
    userId.addEventListener("click",debugEvent);
    d.appendChild(pseudo);
    d.appendChild(userId);
    d.appendChild(b);
    list.appendChild(d);
  }
  document.querySelector("#follows_c").appendChild(list);
}

function setupFollowersList(result){
  closeFollowersList();
  let list = document.createElement("div");
  list.id="followers_list";
  let s = document.createElement("span");
  s.textContent="\u00D7";
  s.addEventListener("click",closeFollowersList);
  list.appendChild(s);
  for(let i =0;i<result.length;i++){
    let d = document.createElement("div");
    d.id="f_list_profile";
    let userId = document.createElement("p");
    userId.textContent="@"+result[i].userId;

    let pseudo = document.createElement("p");
    pseudo.textContent=result[i].pseudo;

    let b = document.createElement("button");
    b.className="f_list_button";
    if(result[i].mutual){
      b.textContent="se désabonner";
     b.addEventListener("click",function(){unfollowFromList(result[i].userId);});
     b.addEventListener("click",debugEvent);

    }
    else{
        b.textContent="s'abonner";
        b.addEventListener("click",function(){followFromList(result[i].userId);});
        b.addEventListener("click",debugEvent);
    }



    pseudo.addEventListener("click",function(){visitProfile(result[i].userId);});
    userId.addEventListener("click",function(){visitProfile(result[i].userId);});
    pseudo.addEventListener("click",debugEvent);
    userId.addEventListener("click",debugEvent);
    d.appendChild(pseudo);
    d.appendChild(userId);
    d.appendChild(b);
    list.appendChild(d);
    }
    document.querySelector("#followers_c").appendChild(list);
  }

function followFromList(userId){
  let data = new FormData();
  data.append("target",userId);
  fetchFromJson("services/follow.php",{method:'POST',body:data,credentials:'same-origin'})
  .then(processToggleSubscribe,displayProfileError);
}

function unfollowFromList(userId){
  let data = new FormData();
  data.append("target",userId);
  fetchFromJson("services/unfollow.php",{method:'POST',body:data,credentials:'same-origin'})
  .then(processToggleSubscribe,displayProfileError);
}

function closeFollowersList(ev){
  if(document.querySelector("#followers_c").innerHTML.includes("followers_list")){
    document.querySelector("#followers_c").removeChild(document.querySelector("#followers_list"));
  }
}

function closeFollowsList(ev){

  if(document.querySelector("#follows_c").innerHTML.includes("follows_list"))
    document.querySelector("#follows_c").removeChild(document.querySelector("#follows_list"));

}

function debugEvent(ev){
  console.log(this.textContent+"triggered!");
}
