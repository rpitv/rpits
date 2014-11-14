<?php

// This file is just for checking that functions / ideas work independently
//
// It's currently a test case for fillRectangle


include("include.php");
include("Geo.php");
include("Primitives.php");

$w = 500;
$h = 500;

$canvas = new Imagick();
$canvas->newImage($w, $h, "none", 'png');
$canvas->setImageDepth(8);
$canvas->setimagecolorspace(imagick::COLORSPACE_SRGB);

$sR = [
		'x' => 10,
		'y' => 10,
		'w' => 400,
		'h' => 60,
		'color' => 'green'
];

$instanceOfSr = new SlantRectangle($sR);
$instanceOfSr->addToCanvas($canvas,true);
//echo $instanceOfSr->toJSON();

header("Content-Type: image/" . IMGFMT);
echo $canvas;


?>

