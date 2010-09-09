<?php

$f = $_GET["f"];
$p = $_GET["p"];
$w = $_GET["w"];
$q = $_GET["q"];
if(!$q)
  $q=90;


$size = getimagesize("$p/$f");

if($w > $size[0])
  $w = $size[0];

$h = floor($size[1]/$size[0]*$w);

$cached = @file_get_contents("$p/$w.$h.$q.$f");
if($cached)
{
  header('Content-Type: image/jpeg');
  echo($cached);
  exit();
}

$image = imagecreatefromjpeg("$p/$f");
$out = imagecreatetruecolor($w,$h);
imagecopyresampled($out,$image,0,0,0,0,$w,$h,$size[0],$size[1]);
imagejpeg($out,"$p/$w.$h.$q.$f",$q);
header('Content-Type: image/jpeg');
imagejpeg($out,NULL,$q);

?>
