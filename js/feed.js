
function getFeed(){
  fetchFromJson("services/findMessages.php",{method:'POST',credentials:'same-origin'})
  .then(processFeed,errorFeed);
}

function processFeed(answer){
  if(answer.status=="ok")
    displayFeed(answer);
  else
    displayFeedError(answer.message,document.querySelector("#messages"));
}

function getFilteredFeed(){
  console.log("récupération du feed personnalisé");
  fetchFromJson("services/findFollowedMessages.php",{method:'POST',credentials:'same-origin'})
  .then(processFeed,errorFeed);
}

function getFeedByAuthor(author){
  let data = new FormData();
  data.append("author",author);
  fetchFromJson("services/findMessages.php",{method:'POST',body:data})
  .then(processFeed,errorFeed);
}

function displayFeed(answer){
  for (let i = 0;i<answer.result.length;i++)
  {
      displayMessage(answer.result[i],document.querySelector("#messages"));
  }
}

function displayMessage(message,element){
  let d = document.createElement('div');
  d.className="message";
  let author = document.createElement('p');
  author.className="author";
  author.textContent ="@"+message.author;

  let content = document.createElement('p');
  content.className="content";
  content.textContent=message.content;

  let date = document.createElement('p');
  date.className="date";
  date.textContent=message.datetime;

  let pseudo = document.createElement('p');
  pseudo.className="pseudo";
  pseudo.textContent=message.pseudo;

  let img = document.createElement('img');
  img.className='profile_picture';
  img.src='services/getAvatar.php?userId='+message.author;
  img.alt='photo de profil';

  d.appendChild(img);
  d.appendChild(pseudo);
  d.appendChild(author);
  d.appendChild(content);
  d.appendChild(date);
  element.appendChild(d);

}
function displayFeedError(error,element){
  let d = document.createElement('div');
  let p = document.createElement('p');
  p.textContent = error;
  element.appendChild(d);
  d.appendChild(p);
}

function errorFeed(error){
  displayError(error,document.querySelector("#messages"));
}
