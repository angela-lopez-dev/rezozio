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
    let context = JSON.parse(document.body.dataset.context);
    document.forms.message_editor.output.textContent="message publie.";
    removeFeed();
    feedGivenContext(context);
  }
  else
    document.forms.message_editor.output.textContent= answer.message;
}

function errorPostMessage(error){
    document.forms.message_editor.output.textContent= answer.message;
}
