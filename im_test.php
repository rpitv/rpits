<?php

// This file is just for checking that functions / ideas work independently
//
// It's currently a test case for fillRectangle


include("imagick_include.php");
$canvas = new Imagick();
$canvas->newImage(700,700,"none","png");

//blackBox($canvas,48,130,862,200);

rectangle($canvas,array('x'=>10,'y'=>10,'w'=>670,'h'=>60,'color'=>'#d00000'));
slantRectangle($canvas,array('x'=>10,'y'=>80,'w'=>670,'h'=>60,'color'=>'#d00000'));


oldSlantRectangle($canvas,array('x'=>10,'y'=>150,'w'=>670,'h'=>60,'color'=>'#d00000'));

slantRectangle($canvas,array('x'=>10,'y'=>220,'w'=>670,'h'=>60,'color'=>'white'));
oldSlantRectangle($canvas,array('x'=>10,'y'=>290,'w'=>670,'h'=>60,'color'=>'white'));


slantRectangle($canvas,array('x'=>10,'y'=>360,'w'=>670,'h'=>60,'color'=>'black'));
oldSlantRectangle($canvas,array('x'=>10,'y'=>430,'w'=>670,'h'=>60,'color'=>'black'));

slantRectangle($canvas,array('x'=>10,'y'=>500,'w'=>670,'h'=>60,'color'=>'none'));
oldSlantRectangle($canvas,array('x'=>10,'y'=>570,'w'=>670,'h'=>60,'color'=>'none'));

$fontN = "fonts/GothamNarrow-Bold.otf";
$west = imagick::GRAVITY_WEST;

//shadowText($canvas,array('x'=>45,'y'=>15,'w'=>600,'h'=>30,'text'=>"Your Imagick install is configured correctly!",'gravity' => "west", 'font' => "fontN", 'color' => "white"));

header("Content-Type: image/png");
echo $canvas;


?>

