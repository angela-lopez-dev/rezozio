function search(ev){
  removeSearch();
  removeErrors();
  fetchFromJson("services/findUsers.php",{method:'POST',body:new FormData(this)})
  .then(processSearch,errorSearch);
}

function processSearch(answer){
  if(answer.status == "ok")
    displayResults(answer.result,document.querySelector("#search_results"));
  else
    displaySearchError("Veuillez entrer au moins 3 caractères.",document.querySelector("#search_results_error"));
}


function displayResults(results,element){
  for (let i = 0;i<results.length;i++){
    displayUser(results[i],element);
  }
}

function errorSearch(error){
  displaySearchError(error,document.querySelector("#search_results_error"));
}

function displayUser(user,element){
  let  d = document.createElement("div");
  d.className="user_as_search_result";

  let userId = document.createElement("p");
  userId.className="login_as_search_result";
  userId.textContent = user.userId;

  let pseudo= document.createElement("p");
  pseudo.className="pseudo_as_search_result";
  pseudo.textContent = user.pseudo;

  d.appendChild(userId);
  d.appendChild(pseudo);
  element.appendChild(d);
  d.addEventListener('click',selectResult);
}

function displaySearchError(error,element){
  let p = document.createElement("p");
  p.textContent = error;
  element.appendChild(p);
}

function removeSearch(){
  document.querySelector("#search_results").innerHTML="";
}
function removeErrors(){
  document.querySelector("#search_results_error").innerHTML="";
}

function selectResult(e){
  document.forms.search_bar.searchedString.value = this.querySelector(".login_as_search_result").textContent;
  removeSearch();
}
