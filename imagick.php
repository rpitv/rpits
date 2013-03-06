<?php
include("imagick_include.php");
$canvas = new Imagick();
$canvas->newImage(700,60,"none","png");

//blackBox($canvas,48,130,862,200);

slantRectangle($canvas,10,10,670,40,'#d00000');

$fontN = "fonts/GothamNarrow-Bold.otf";
$west = imagick::GRAVITY_WEST;

shadowedText($canvas,array('x'=>45,'y'=>15,'w'=>600,'h'=>30,'text'=>"Your Imagick install is configured correctly!",'gravity' => "west", 'font' => "fontN", 'color' => "white"));

header("Content-Type: image/png");
echo $canvas;

?>
