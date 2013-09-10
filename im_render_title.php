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

timestamp ('pre-Imagick');

$canvas = new Imagick();
$canvas->newImage(1920, 1080, "none", IMGFMT);
$canvas->setImageDepth(8);

timestamp('post allocation');

foreach ($title['geos'] as $geo) {
	addGeoToCanvas($canvas,$geo,$bustCache);
}

timestamp ('post geos');

// Display canvas as png image when php page is requested.
if(!$metrics) {
	header("Content-Type: image/" . IMGFMT);
	echo $canvas;
}

// Generate thumbnail image of the title for UI purposes.

$thumb = $canvas->clone();
$thumb->cropImage(1440, 1080, 0, 0);
$thumb->resizeImage(53, 40, Imagick::FILTER_TRIANGLE, 1);
$thumb->writeImage(realpath('thumbs') . '/' . $title["name"] . $title["id"] . '.' . IMGFMT);

timestamp('post thumbs');

// Generate the output file of the title.
$canvas->writeImage(realpath('out') . '/' . $title["name"] . $title["id"] . '.' . IMGFMT);

timestamp ('post out');
?>
