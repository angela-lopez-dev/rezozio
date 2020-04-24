<?php
  /**
  *Mets l'image reçue aux dimensions 48*48 et 256*256 puis la stocke.
  *args : image (fichier)
  *requête via post uniquement
  *réponse {true}*/
  //require("../lib/watchdog_service.php");
  require_once("../lib/common_service.php");
  require_once("../lib/img_utils.php");
  try{
    $userId = $_SESSION['id'];
    $data = new DataLayer();
    //vérification de l'existence de l'image
    if(!isset($_FILES['image'])){
      produceError('Image manquante.');
    }
    //créer les images à partir du flux
    $name = $_FILES['image']['tmp_name'];
    $type = $FILES_['image']['type'];
    $flux = fopen($name,'r');
    $src = createImageFromStream($flux);
    //redimensionner en 48*48 et 256*256
    $small = resizeSquare(copyCenterSquare($src),IMG_SMALL);
    $large = resizeSquare(copyCenterSquare($src),IMG_LARGE);
    $fluxSmall = setFlux($small);
    $fluxLarge = setFlux($large);
    //stocker dans la base de données
    $data->storeAvatar(['avatar_small'=>$fluxSmall,'avatar_large'=>$fluxLarge,'mimetype'=>'image/png'],$login);
    //produire la réponse au format demandé
    fclose($fluxSmall);
    fclose($fluxLarge);
    if($data)
      produceResult(true);
    else
      produceError('L\'utilisateur demandé n\'existe pas.');
  }catch(PDOException $e){
    produceError($e->getMessage());
  }

 ?>
