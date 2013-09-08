<?php

include("include.php");
include("imagick_include.php");

$name = $_GET["name"];
$id = $_GET["id"];
$type = $_GET["type"];

$attr = dbFetchAll($id, $name, $type);

//print_r($attr);

$canvas = new Imagick();
$canvas->newImage($attr["w"] + 20, $attr["h"] + 30, "none", "png");

if ($type == "slantRectangle")
	slantRectangle($canvas, 10, 10, $attr["w"], $attr["h"], $attr["color"]);
else if ($type == "blackBox")
	blackBox($canvas, 10, 10, $attr["w"], $attr["h"]);
else if ($type == "plainText")
	plainText($canvas, 10, 10, $attr["w"], $attr["h"], $attr["text"], $attr["gravity"], $attr["font"], $attr["color"]);
else if ($type == "shadowText")
	shadowText($canvas, 10, 10, $attr["w"], $attr["h"], $attr["text"], $attr["gravity"], $attr["font"], $attr["color"]);
else if ($type == "placeImage")
	placeImage($canvas, 10, 10, $attr["w"], $attr["h"], $attr["path"]);

header("Content-Type: image/png");
echo $canvas;
?>
