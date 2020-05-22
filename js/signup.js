function signup(ev){
  console.log('signup');
  ev.preventDefault();
  fetchFromJson('services/createUser.php',{method:'POST',body:new FormData(this),credentials:'same-origin'})
  .then(processSignup,errorSignup);
}

function processSignup(answer){
  if(answer.status=="ok")
    document.forms.form_signup.message.textContent = "Votre compte a bien été créé. Veuillez vous connecter pour profiter de Rezozio !";
  else
    document.forms.form_signup.message.textContent = answer.message;
}
function errorSignup(error){
    document.forms.form_signup.message.textContent = error;
}
