function openMessageEditor(){
  document.querySelector("#message_editor_container").hidden=false;
  document.querySelector("#message_editor_container span.close").addEventListener("click",closeMessageEditor);
}

function closeMessageEditor(){
  document.querySelector("#message_content").value ="";
  document.querySelector("#message_editor_container").hidden=true;
}

function postMessage(ev){
  ev.preventDefault();
  fetchFromJson("services/postMessage.php",{method:'POST',body:new FormData(this),credentials:'same-origin'})
  .then(processPostMessage,errorPostMessage);
}

function processPostMessage(answer){
  if(answer.status == "ok"){
    document.forms.message_editor.output.textContent="message publie.";
    //si on n'est pas en train de visiter le profil de quelqu'un d'autre que soi
    if(document.querySelector("#userProfile").textContent=="")
      //rafraîchir les messages
      loggedInMode();
    else if(JSON.parse(document.body.dataset.user).userId == document.querySelector("#userProfile p.profile_userId").textContent.substring(1))
      goToOwnProfile();
  }
  else
    document.forms.message_editor.output.textContent= answer.message;
}

function errorPostMessage(error){
    document.forms.message_editor.output.textContent= answer.message;
}
