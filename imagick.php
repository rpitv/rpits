<?php
include("imagick_include.php");
$canvas = new Imagick();
$canvas->newImage(700,60,"none","png");

//blackBox($canvas,48,130,862,200);

slantRectangle($canvas,array('x'=>10,'y'=>10,'w'=>670,'h'=>40,'color'=>'#d00000'));

$fontN = "fonts/GothamNarrow-Bold.otf";
$west = imagick::GRAVITY_WEST;

shadowText($canvas,array('x'=>45,'y'=>15,'w'=>600,'h'=>30,'text'=>"Your Imagick install is configured correctly!",'gravity' => "west", 'font' => "fontN", 'color' => "white"));

header("Content-Type: image/png");
echo $canvas;

?>
