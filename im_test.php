<?php

// This file is just for checking that functions / ideas work independently
//
// It's currently a test case for fillRectangle


include("include.php");
//include("imagick_include.php");

$gradient = $_GET['gradient'];
$w = 600;
$h = 300;

$canvas = new Imagick();
$canvas->newImage($w, $h, "none", 'png');
$canvas->setImageDepth(8);
$canvas->setimagecolorspace(imagick::COLORSPACE_SRGB);

$result = fillRectangle($w,$h,$gradient);
$canvas->compositeImage($result,Imagick::COMPOSITE_OVER,0,0);

header("Content-Type: image/" . IMGFMT);
echo $canvas;


?>

