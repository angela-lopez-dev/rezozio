function goHome(ev)
{
  removeMenu();
  removeProfile();
  initState();

}

function removeMenu(){
  console.log("removing menu");
  document.querySelector("#home").innerHTML="";
  document.querySelector("#profile").innerHTML="";
}
