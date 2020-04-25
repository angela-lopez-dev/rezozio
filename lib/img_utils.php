<?php

const IMG_SMALL = 48;
const IMG_LARGE = 256;
/*
 * Returns image ressource initialized with data from $stream (auto-detect format)
 */
 function createImageFromStream($stream){
   return imagecreatefromstring(stream_get_contents($stream));
}
/*
* Returns image ressource initialized with data from $fileName (auto-detect format)
*/
  function createImageFromFile($fileName){
   return imagecreatefromstring(file_get_contents($fileName));
}
/*Renvoie la valeur du plus grand carré possible pour les dimensions données en paramètre*/
  function greatestSquare($largeur,$hauteur)
  {
    $res=($largeur>$hauteur)?$hauteur:$largeur;
    return $res;
  }
/**
*prend en argument une ressource image et produit une
*copie du plus grand carré possible au centre d'une nouvelle image redimensionne au format $dimension*/
function copyResizeFromCenter($img_src,$dimension){
  
  $largeur = imagesx($img_src);
  $hauteur = imagesy($img_src);
  $c = greatestSquare($largeur,$hauteur);
  $imageCarre = imagecreatetruecolor($dimension,$dimension);
  //met le plus grand carré possible dans img dst à partir de img src.
  imagecopyresampled($imageCarre, $img_src, 0, 0, ($largeur-$c)/2, ($hauteur-$c)/2, $dimension, $dimension, $c, $c);
  return $imageCarre;
}


  /**
  *prend en argument une ressource image et renvoie un flux sur cette image en png */
  function setFlux($img)
  {
    $fluxTmp = fopen("php://temp","r+");
    imagepng($img,$fluxTmp);
    rewind($fluxTmp);
    return $fluxTmp;
  }


 ?>
