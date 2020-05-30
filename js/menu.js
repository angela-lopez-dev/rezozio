function goHome(ev)
{
  removeMenu();
  removeProfile();
  initState();

}

function removeMenu(){
  console.log("removing menu");
  document.querySelector("#home").innerHTML="";
  document.querySelector("#menu_connecte").style.visibility="hidden";
  document.querySelector("#profile").innerHTML="";
}
