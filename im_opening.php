<?php
include("imagick_include.php");


$fontN = "fonts/GothamNarrow-Bold.otf";
$font = "fonts/Gotham-Bold.ttf";
$fontX = "fonts/GothamXNarrow-Bold.otf";

$east = imagick::GRAVITY_EAST;
$center = imagick::GRAVITY_CENTER;
$west = imagick::GRAVITY_WEST;

$canvas = new Imagick();
$canvas->newImage(1920,1080,"gray","png");

slantRectangle($canvas,630,330,710,60,'#333',0);
slantRectangle($canvas,660,200,880,130,'#F00',0);
slantRectangle($canvas,360,180,380,230,'#fff',0);

slantRectangle($canvas,630,610,710,60,'#333',0);
slantRectangle($canvas,660,480,880,130,'#080',0);
slantRectangle($canvas,360,460,380,230,'#fff',0);

slantRectangle($canvas,365,930,500,50,'#333',0);
slantRectangle($canvas,390,860,700,70,'#F00',0);




header("Content-Type: image/png");
echo $canvas;
?>
