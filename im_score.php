<?php

include("imagick_include.php");


$fontN = "fonts/GothamNarrow-Bold.otf";
$font = "fonts/Gotham-Bold.ttf";
$fontX = "fonts/GothamXNarrow-Bold.otf";

$east = imagick::GRAVITY_EAST;
$center = imagick::GRAVITY_CENTER;
$west = imagick::GRAVITY_WEST;

$canvas = new Imagick();
$canvas->newImage(1920,1080,"none","png");

slantRectangle($canvas,660,820,600,80,'#d00000',0);
slantRectangle($canvas,660,680,600,80,'#008000',0);

slantRectangle($canvas,520,800,200,120,'#fff',0);
slantRectangle($canvas,520,660,200,120,'#fff',0);
placeLogo($canvas,570,808,100,100,"rpi");
placeLogo($canvas,570,668,100,100,"dart");
//slantRectangle($canvas,520,800,200,120,'white',"teamlogos/rpi.png");
//slantRectangle($canvas,520,660,200,120,'white',"teamlogos/dart.png");

slantRectangle($canvas,1200,800,200,120,'#333',0);
slantRectangle($canvas,1200,660,200,120,'#333',0);

slantRectangle($canvas,680,940,470,60,'#333',0);

shadowedText($canvas,720,690,480,60,"DARTMOUTH",$west,$font,"white");
shadowedText($canvas,720,830,480,60,"RENSSELAER",$west,$font,"white");

shadowedText($canvas,1200,670,200,110,"0",$center,$font,"white");
shadowedText($canvas,1200,810,200,110,"3",$center,$font,"white");

shadowedText($canvas,720,950,390,40,"End of 1st Period",$center,$fontN,"white");





header("Content-Type: image/png");
echo $canvas;

?>
