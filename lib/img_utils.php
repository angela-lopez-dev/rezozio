<?php

const IMG_SMALL = 48;
const IMG_LARE = 256;
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
*copie du plus grand carré possible au centre d'une nouvelle image*/
  function copyCenterSquare($img)
  {
    $img_largeur = imagesx($img);
    $img_hauteur = imagesy($img);
    $sq_dim = greatestSquare($img_largeur,$img_hauteur);
    $dst_img = imagecreatetruecolor($sq_dim,$sq_dim);
    imagecopyresampled(
      $dst_img,$img,
      0,0,($img_largeur-$sq_dim)/2,($img_hauteur-$sq_dim)/2,
      $sq_dim,$sq_dim,$img_largeur,$img_hauteur
    );
    return $dst_img;
  }

  /**prend en argument une image carrée et lui fait prendre les dimensions
  dst_dim*dst_dim*/
  function resizeSquare($img,$dst_dim)
  {
    $src_dim = imagesx($img);
    $dst_img = imagecreatetruecolor($dst_dim,$dst_dim);
    imagecopyresampled($dst_img,$img,
    0,0,0,0,
    $dst_dim,$dst_dim,$src_dim,$src_dim);
    return $dst_img;
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
