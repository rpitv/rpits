<?php
include("imagick_include.php");
$canvas = new Imagick();
$canvas->newImage(1000,1000,"gray","png");

slantRectangle($canvas,50,50,900,80,'#d00000');
slantRectangle($canvas,50,150,900,80,'#d0d000');
slantRectangle($canvas,50,250,900,80,'#00d000');
slantRectangle($canvas,50,350,900,80,'#00d0d0');
slantRectangle($canvas,50,450,900,80,'#0000d0');
slantRectangle($canvas,50,550,900,80,'#d000d0');
slantRectangle($canvas,50,650,500,340,'#d00000');

slantRectangle($canvas,550,650,400,40,'#d00000');
slantRectangle($canvas,520,710,430,40,'#d0d000');
slantRectangle($canvas,490,770,460,40,'#00d000');
slantRectangle($canvas,460,830,490,40,'#00d0d0');
slantRectangle($canvas,430,890,520,40,'#0000d0');
slantRectangle($canvas,400,950,550,40,'#d000d0');
slantRectangle($canvas,50,650,150,40,'#d00000');
slantRectangle($canvas,50,710,120,40,'#d0d000');
slantRectangle($canvas,50,770,90,40,'#00d000');
slantRectangle($canvas,50,830,60,40,'#00d0d0');
slantRectangle($canvas,50,890,30,40,'#0000d0');

$fontN = "fonts/GothamNarrow-Bold.otf";
$west = imagick::GRAVITY_WEST;

shadowedText($canvas,90,60,820,60,"Reilly Hamilton",$west,$fontN,"white");
shadowedText($canvas,90,160,820,60,"Longer text that auto-resizes perfectly",$west,$fontN,"white");
shadowedText($canvas,450,895,480,30,"Created using Imagick and PHP",$west,$fontN,"white");
shadowedText($canvas,420,955,510,30,"Dynamically generated through functions",$west,$fontN,"white");


header("Content-Type: image/png");
echo $canvas;

?>
