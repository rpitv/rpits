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



blackbox($canvas, array('x' => 400, 'y' => 870 - $boxHeightModifier, 'w' => 1120, 'h' => 160 + $boxHeightModifier));
slantRectangle($canvas, array('x' => 360, 'y' => 800 - $boxHeightModifier, 'w' => 780, 'h' => 80, 'color' => $tColor));
slantRectangle($canvas, array('x' => 1100 - $positionWidthModifier, 'y' => 800 - $boxHeightModifier, 'w' => 150, 'h' => 80, 'color' => "#303030"));
slantRectangle($canvas, array('x' => 1210 - $positionWidthModifier, 'y' => 800 - $boxHeightModifier, 'w' => 130 + $positionWidthModifier, 'h' => 80, 'color' => $tColor));
slantRectangle($canvas, array('x' => 1300, 'y' => 800 - $boxHeightModifier, 'w' => 140, 'h' => 80, 'color' => "#303030"));
slantRectangle($canvas, array('x' => 1400, 'y' => 800 - $boxHeightModifier, 'w' => 160, 'h' => 80, 'color' => "white"));

$pPath = "teams/" . $row["team"] . "imgs/" . $row["first"] . $row["last"] . ".png";
$size = @getimagesize($pPath);

$nameModifier = 0;
$detailsModifier = 0;

if ($size[0]) {

	$p = array('w' => 192, 'h' => 230, 'x' => 400, 'y' => '801', 'path' => $pPath);

	if ($size[0] * 1.2 > $size[1]) {
		$p['h'] = $size[1] / ($size[0] / $p['w']);
		$p['y'] += 230 - $p['h'];
	}

	placeImage($canvas, $p);
} else {
	$nameModifier = -150;
	$detailsModifier = -220;
}

placeImage($canvas, array('x' => 1442, 'y' => 802 - $boxHeightModifier, 'w' => 76, 'h' => 76, 'path' => "teamlogos/" . $teamrow["logo"]));

shadowedText($canvas, array('x' => 560 + $nameModifier, 'y' => 805 - $boxHeightModifier, 'w' => 535 - $nameModifier - $positionWidthModifier, 'h' => 70, 'text' => $row["first"] . " " . $row["last"], 'gravity' => "west", 'font' => "fontN", 'color' => "white"));
shadowedText($canvas, array('x' => 1100 - $positionWidthModifier, 'y' => 800 - $boxHeightModifier, 'w' => 150, 'h' => 80, 'text' => $row["num"], 'gravity' => "center", 'font' => "fontN", 'color' => "white"));
shadowedText($canvas, array('x' => 1210 - $positionWidthModifier, 'y' => 800 - $boxHeightModifier, 'w' => 130 + $positionWidthModifier, 'h' => 80, 'text' => $row["pos"], 'gravity' => "center", 'font' => "fontN", 'color' => "white"));
shadowedText($canvas, array('x' => 1300, 'y' => 800 - $boxHeightModifier, 'w' => 140, 'h' => 80, 'text' => $row["year"], 'gravity' => "center", 'font' => "fontN", 'color' => "white"));

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
plainText($canvas, array('x' => 630 + $detailsModifier, 'y' => 884 - $boxHeightModifier, 'w' => 880 - $detailsModifier, 'h' => 33, 'text' => $details, 'gravity' => $detailsGravity, 'font' => "fontN", 'color' => "white"));

if ($stype && $stype != "txt") {
	if ($lastSeason == true) {
		shadowedText($canvas, array('x' => 410, 'y' => 995, 'w' => 172, 'h' => 30, 'text' => 'Last Season:', 'gravity' => "center", 'font' => "fontN", 'color' => "white"));
	} else if ($row["team"] == career) {
		shadowedText($canvas, array('x' => 410, 'y' => 995, 'w' => 172, 'h' => 30, 'text' => 'Career Stats:', 'gravity' => "center", 'font' => "fontN", 'color' => "white"));
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
		$totalwidths += getTextWidth(array('w' => $boxW, 'h' => 80, 'text' => $row[$j + 8], 'font' => "fontN"));
	}


	//echo $totalwidths;
	$spacing = ($statsBoxWidth - $totalwidths) / ($i - 2);
	//plainText($canvas,50,50,300,50,$i-2 . ", $boxW, $totalwidths, $spacing ","left","fontN","white");


	for ($j = 2; $j < $i; $j++) {
		$thisWidth = getTextWidth(array('w' => $boxW, 'h' => 80, 'text' => $row[$j + 8], 'font' => "fontN"));
		$statsBoxX -=($boxW - $thisWidth) / 2;
		plainText($canvas, array('x' => $statsBoxX, 'y' => 915, 'w' => $boxW, 'h' => 40, 'text' => $slabel[$j], 'gravity' => "center", 'font' => "fontN", 'color' => "white"));
		plainText($canvas, array('x' => $statsBoxX, 'y' => 955, 'w' => $boxW, 'h' => 80, 'text' => $row[$j + 8], 'gravity' => "center", 'font' => "fontN", 'color' => "white"));
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
