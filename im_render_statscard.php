<?php

include("include.php");
include("imagick_include.php");

$id = $_GET["id"];
$cacheno = $_GET["c"];
$lastSeason = false;


mysql_select_db("rpihockey");

$result = dbquery("SELECT * from players WHERE `id` = '$id'");
$row = mysql_fetch_array($result);

$result = dbquery("SELECT * from teams WHERE `name` = '" . $row["team"] . "'");
$teamrow = mysql_fetch_array($result);
$tColor = rgbhex($teamrow["colorr"], $teamrow["colorg"], $teamrow["colorb"]);

$stype = $row["stype"];
if ($stype != "txt") {
	$result = dbquery("SELECT * FROM stattype WHERE `type`  = '$stype'");
	$slabel = mysql_fetch_array($result);
}

mysql_select_db("rpits");

//
// CACHING SECTION
//

foreach ($row as $item) {
	$hash .= $item;
}
$key = $row["num"] . $row["first"] . $row["last"];
$hash = addslashes($hash);
$oldkey = $key;
$key = addslashes($key);

$result = dbquery("SELECT * from cache WHERE `key` = '$key'");
$cacherow = mysql_fetch_array($result);
$cacherow["hash"] = addslashes($cacherow["hash"]);

if ($cacherow["hash"] == $hash && $cacheno != 1) {
	$png = file_get_contents("out/$oldkey.png");
	if ($png) {
		header('Content-Type: image/png');
		echo($png);
		exit();
	}
}

$boxHeightModifier = 0;
$positionWidthModifier = 0;

// Check to see if there are stats
if (!$stype) {
	$boxHeightModifier = -113;
}

// Check to see if position uses two characters
if ($row["pos"][1]) {
	$positionWidthModifier = 50;
}
if ($row["pos"][0] == 'W') {
	$positionWidthModifier += 20;
}


$canvas = new Imagick();
$canvas->newImage(1920, 1080, "none", "png");



blackbox($canvas, 400, 870 - $boxHeightModifier, 1120, 160 + $boxHeightModifier);
slantRectangle($canvas, 360, 800 - $boxHeightModifier, 780, 80, $tColor);
slantRectangle($canvas, 1100 - $positionWidthModifier, 800 - $boxHeightModifier, 150, 80, "#303030");
slantRectangle($canvas, 1210 - $positionWidthModifier, 800 - $boxHeightModifier, 130 + $positionWidthModifier, 80, $tColor);
slantRectangle($canvas, 1300, 800 - $boxHeightModifier, 140, 80, "#303030");
slantRectangle($canvas, 1400, 800 - $boxHeightModifier, 160, 80, "white");

$pPath = "teams/" . $row["team"] . "imgs/" . $row["first"] . $row["last"] . ".png";
$size = @getimagesize($pPath);

$nameModifier = 0;
$detailsModifier = 0;

if ($size[0]) {
	$pW = 192;
	$pH = 230;
	$pX = 400;
	$pY = 801;

	if ($size[0] * 1.2 > $size[1]) {
		$pH = $size[1] / ($size[0] / $pW);
		$pY += 230 - $pH;
	}

	placeImage($canvas, $pX, $pY, $pW, $pH, $pPath);
} else {
	$nameModifier = -150;
	$detailsModifier = -220;
}

placeImage($canvas, 1442, 802 - $boxHeightModifier, 76, 76, "teamlogos/" . $teamrow["logo"]);

shadowedText($canvas, 560 + $nameModifier, 805 - $boxHeightModifier, 535 - $nameModifier - $positionWidthModifier, 70, $row["first"] . " " . $row["last"], "west", "fontN", "white");
shadowedText($canvas, 1100 - $positionWidthModifier, 800 - $boxHeightModifier, 150, 80, $row["num"], "center", "fontN", "white");
shadowedText($canvas, 1210 - $positionWidthModifier, 800 - $boxHeightModifier, 130 + $positionWidthModifier, 80, $row["pos"], "center", "fontN", "white");
shadowedText($canvas, 1300, 800 - $boxHeightModifier, 140, 80, $row["year"], "center", "fontN", "white");

$details = "Hometown: " . $row["hometown"] . "       Ht: " . $row["height"];
if ($row["weight"] . length > 0) {
	$details .= "       Wt: " . $row["weight"];
}
$detailsGravity = "west";

if (!$size[0]) {
	$details = "Hometown: " . $row["hometown"] . "       Height: " . $row["height"];
	if ($row["weight"] . length > 0) {
		$details .= "       Weightt: " . $row["weight"];
	}
	$detailsGravity = "center";
}
plainText($canvas, 630 + $detailsModifier, 884 - $boxHeightModifier, 880 - $detailsModifier, 33, $details, $detailsGravity, "fontN", "white");

if ($stype && $stype != "txt") {
	if ($lastSeason == true) {
		shadowedText($canvas, 410, 995, 172, 30, 'Last Season:', 'center', 'fontN', 'white');
	} else if ($row["team"] == career) {
		shadowedText($canvas, 410, 995, 172, 30, 'Career Stats:', 'center', 'fontN', 'white');
	}
	$statsBoxWidth = 880;
	$statsBoxX = 650;
	if (!$size[0]) {
		$statsBoxX = 525;
		$statsBoxWidth = 950;
	}
	$i = 2;
	for (; strlen($slabel[$i]) > 0; $i++) {

	}

	$boxW = $statsBoxWidth / ($i - 2);
	$totalwidths = 0;
	for ($j = 2; $j < $i; $j++) {
		$totalwidths += getTextWidth($boxW, 80, $row[$j + 8], "fontN");
	}


	//echo $totalwidths;
	$spacing = ($statsBoxWidth - $totalwidths) / ($i - 2);
	//plainText($canvas,50,50,300,50,$i-2 . ", $boxW, $totalwidths, $spacing ","left","fontN","white");


	for ($j = 2; $j < $i; $j++) {
		$thisWidth = getTextWidth($boxW, 80, $row[$j + 8], "fontN");
		$statsBoxX -=($boxW - $thisWidth) / 2;
		plainText($canvas, $statsBoxX, 915, $boxW, 40, $slabel[$j], "center", "fontN", "white");
		plainText($canvas, $statsBoxX, 955, $boxW, 80, $row[$j + 8], "center", "fontN", "white");
		$statsBoxX += ($boxW - $thisWidth) / 2 + $thisWidth + $spacing;
	}
}

dbquery("REPLACE INTO cache SET `key` = '$key', `hash` = '$hash';");

$filename = $row["num"] . $row["first"] . $row["last"];

$canvas->setImageDepth(8);

$canvas->writeImage('out/' . $filename . '.png');

$thumb = $canvas->clone();
$thumb->cropImage(318, 239, 398, 794);
$thumb->resizeImage(53, 40, Imagick::FILTER_TRIANGLE, 1);
$thumb->writeImage('thumbs/' . $filename . '.png');

header("Content-Type: image/png");
echo $canvas;
?>
