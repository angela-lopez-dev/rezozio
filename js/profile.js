//****** CONSULTER UN PROFIL D'UTILISATEUR*********//

function goToProfile(ev){
  ev.preventDefault();
  //afficher le profil texte de l'utilisateur
  refreshProfile(this.searchedString.value);
  removeFeed();
  getFeedByAuthor(this.searchedString.value);

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
  else{
    displayFeedError(answer.error,document.querySelector("#userProfile"));
  }
}

function errorGoToProfile(error){
  displayFeedError(error,document.querySelector("#userProfile"));
}

 function setupProfile(profile,element){
   let userId,pseudo,description,subscriptions,isFollower,block;

   userId = document.createElement("p");
   userId.className="profile_userId";
   userId.textContent="@"+profile.userId;

   pseudo = document.createElement("p");
   pseudo.className="profile_pseudo";
   pseudo.textContent=profile.pseudo

   description = document.createElement("p");
   description.className="profile_description";
   description.textContent=profile.description;

   let img = document.createElement('img');
   img.className='profile_picture';
   img.src='services/getAvatar.php?size=large&userId='+profile.userId;
   img.alt='photo de profil';

  element.appendChild(img);
  element.appendChild(pseudo);
  element.appendChild(userId);
  element.appendChild(description);
//mode connecté et le profil visité n'est pas le sien
  if(profile.hasOwnProperty("followed") && JSON.parse(document.body.dataset.user).userId !== profile.userId){
  subscriptions=document.createElement("button");
  subscriptions.className="subscriptions_button";
  console.log(profile);
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

 }

function follow(){
  let data = new FormData();
  data.append("target",document.querySelector(".profile_userId").textContent.substring(1));
  fetchFromJson("services/follow.php",{method:'POST',body:data,credentials:'same-origin'})
  .then(processToggleSubscribe,errorToggleSubscribe);
}

function unfollow(){
  let data = new FormData();
  data.append("target",document.querySelector(".profile_userId").textContent.substring(1));
  fetchFromJson("services/unfollow.php",{method:'POST',body:data,credentials:'same-origin'})
  .then(processToggleSubscribe,errorToggleSubscribe);
}

function processToggleSubscribe(answer){
  if(answer.status == "ok")
    refreshProfile(document.querySelector(".profile_userId").textContent.substring(1))
  else
    displayProfileError(answer.message);
}

function errorToggleSubscribe(error){
  displayProfileError(error);
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
}

function blockUser(ev){
  let data = new FormData();
  data.append("target",document.querySelector(".profile_userId").textContent.substring(1));
  fetchFromJson("services/blockUser.php",{method:'POST',body:data,credentials:'same-origin'})
  .then(processBlock,errorBlock);
}

function processBlock(answer){
  if(answer.status == "ok")
    refreshProfile(document.querySelector(".profile_userId").textContent.substring(1))
  else
    displayProfileError(answer.message);
}

function errorBlock(error){
  displayProfileError(error);
}

function unblockUser(ev){
  let data = new FormData();
  data.append("target",document.querySelector(".profile_userId").textContent.substring(1));
  fetchFromJson("services/unblockUser.php",{method:'POST',body:data,credentials:'same-origin'})
  .then(processBlock,errorBlock);
}
