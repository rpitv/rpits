<?php
include("imagick_include.php");
$canvas = new Imagick();
$canvas->newImage(700,60,"none","png");

//blackBox($canvas,48,130,862,200);

slantRectangle($canvas,10,10,670,40,'#d00000');

$fontN = "fonts/GothamNarrow-Bold.otf";
$west = imagick::GRAVITY_WEST;

shadowedText($canvas,45,15,600,30,"Your Imagick install is configured correctly!","west","fontN","white");

header("Content-Type: image/png");
echo $canvas;

?>
