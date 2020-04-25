<?php

  /**
  *args : image (envoyé par POST )
  *result : true ou null si erreur.
  */
  //require("lib/watchdog.php");
  require_once('../lib/DataLayer.class.php');
  require_once('../lib/common_service.php');
  require_once('../lib/img_utils.php');

  if(!isset($_FILES['image'])){
    produceImgError("Impossible de changer l'avatar. Aucune image n'a été fournie.");
    return;
  }
  try{
    $name= $_FILES['image']['tmp_name'];
    $type = $_FILES['image']['type'];
    //convertir avatar en small et large
    $image = createImageFromFile($name);
    $small = copyResizeFromCenter($image,IMG_SMALL);
    $large = copyResizeFromCenter($image,IMG_LARGE);
    $flux_small = setFlux($small);
    $flux_large = setFlux($large);
    //stocker dans des flux temporaires puis dans la bdd
    //$login = $_SESSION['ident']->login;
    $login = 'mallani';
    $imageSpec = ['avatar_small'=>$flux_small,'avatar_large'=>$flux_large,'mimetype'=>$type];
    $data = new DataLayer();
    $res = $data->storeAvatar($imageSpec,$login);
    if($res)
      produceImgResult(true);
    else
      produceImgError("Impossible de changer l'avatar. L'utilisateur n'existe pas.");
  }
  catch(PDOException $e){
    produceImgError($e->getMessage());
  }
  //fclose($imageSpec['data']);
 ?>
