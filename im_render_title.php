<?php

include_once("include.php");
include_once("imagick_include.php");

$titleId = $_GET["id"];
$eventId = $_GET["eventId"];
$path = $_GET["path"];
$player = $_GET["player"];
$bustCache = $_GET['bustCache'] || false;

$title;
$filename;
$key;

if ($path) {
	$title = getTitleFromXML($path);
} else if ($player) {
	$title = getStatscard($player);
	$filename = $title["num"] . $title["first"] . $title["last"];
	$key = 'p'.$title['id'];
} else {
	$title = getTitle($titleId,$eventId);
	$filename = $title["name"] . $title["id"];
	$key = $titleId;
}

if(checkHashForTitle($title,$key) && $bustCache == false) {
	$img = file_get_contents(realpath('out') . '/' . $filename . '.' . IMGFMT);
	timestamp('Echoing cached version');
	if($img && !$metrics) {
		header("Content-Type: image/" . IMGFMT);
		echo $img;
	}
	exit();
}

timestamp ('pre-Imagick');

$canvas = new Imagick();
$canvas->newImage(1920, 1080, "none", IMGFMT);
$canvas->setImageDepth(8);
$canvas->setimagecolorspace(imagick::COLORSPACE_SRGB);

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
$thumb->writeImage(realpath('thumbs') . '/' . $filename . '.' . IMGFMT);

timestamp('post thumbs');

// Generate the output file of the title.
$canvas->writeImage(realpath('out') . '/' . $filename . '.' . IMGFMT);

dbquery("REPLACE INTO cache SET `key`='$key', `hash`='" . getHashForTitle($title) . "';");

timestamp ('post out');
?>
