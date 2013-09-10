<?php

include("include.php");
include("imagick_include.php");

$id = $_GET["id"];

$path = $_GET["path"];

$bustCache = $_GET['bustCache'] || false;

$title;

if ($path) {
	$title = getTitleFromXML($path);
} else {
	$title = getTitle($id);
}

$canvas = new Imagick();
$canvas->newImage(1920, 1080, "none", "png");

foreach ($title['geos'] as $geo) {
	addGeoToCanvas($canvas,$geo,$bustCache);
}

// Display canvas as png image when php page is requested.
header("Content-Type: image/png");
echo $canvas;

// Generate thumbnail image of the title for UI purposes.
$thumb = $canvas->clone();
$thumb->cropImage(1440, 1080, 0, 0);
$thumb->resizeImage(53, 40, Imagick::FILTER_TRIANGLE, 1);
$thumb->writeImage(realpath('thumbs') . '/' . $titleRow["name"] . $titleRow["id"] . '.png');

// Generate the output file of the title.
$canvas->setImageDepth(8);
$canvas->writeImage(realpath('out') . '/' . $titleRow["name"] . $titleRow["id"] . '.png');
?>
