<?php

include("include.php");

$name = $_GET["name"];
$id = $_GET["id"];
$eventId = $_GET['eventId'];
$type = $_GET["type"];

$geo = getTitle($id,$eventId,true)['geos'][$name];

//print_r($geo);

$canvas = new Imagick();
$canvas->newImage($geo["w"] + 20, $geo["h"] + 30, "none", "png");

$geo['x'] = 10;
$geo['y'] = 10;

addGeoToCanvas($canvas, $geo);

header("Content-Type: image/png");
echo $canvas;
?>
