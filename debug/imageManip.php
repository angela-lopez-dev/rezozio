<?php
/*header('Content-type:'.$imgSpec['mimetype']);
imagepng($img_src);*/
require_once("../lib/DataLayer.class.php");
require_once("../lib/img_utils.php");
const IMG_SMALL = 48;
const IMG_LARGE = 256;
$size = "small";
$dimension = IMG_LARGE;
if($size == "small")
  $dimension = IMG_SMALL;
$data = new DataLayer();
$imgSpec = $data->debugGetAvatar('mallani');
$img_src = createImageFromStream($imgSpec['data']);
$imageCarre = copyResizeFromCenter($img_src,IMG_LARGE);
header('Content-type:'.$imgSpec['mimetype']);
imagepng($imageCarre);
?>
