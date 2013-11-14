<?php

include("include.php");
include("imagick_include.php");

$id = $_GET["id"];
$cacheno = $_GET["c"];
$lastSeason = false;

$result = dbquery("SELECT * from players WHERE `id` = '$id'");
$row = mysql_fetch_array($result);

$result = dbquery("SELECT * from statscard_teams WHERE `name` = '" . $row["team"] . "'");
$teamrow = mysql_fetch_array($result);
$tColor = rgbhex($teamrow["colorr"], $teamrow["colorg"], $teamrow["colorb"]);

$stype = $row["stype"];
if ($stype != "txt") {
	$result = dbquery("SELECT * FROM stattype WHERE `type`  = '$stype'");
	$slabel = mysql_fetch_array($result);
}

//
// CACHING SECTION
//

/* No caching
/
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
*/

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

$blackBox = new Imagick();
$blackBox->newImage(1150, 190, "none", "png");

$mainBar = new Imagick();
$mainBar->newImage(1230, 110, "none", "png");

$logoBox = new Imagick();
$logoBox->newImage(190, 110, "none", "png" );

blackbox($blackBox, array('x' => 10, 'y' => 10, 'w' => 1120, 'h' => 160 + $boxHeightModifier));

slantRectangle($mainBar, array('x' => 10, 'y' => 10, 'w' => 780, 'h' => 80, 'color' => $tColor));
slantRectangle($mainBar, array('x' => 750 - $positionWidthModifier, 'y' => 10, 'w' => 150, 'h' => 80, 'color' => "#303030"));
slantRectangle($mainBar, array('x' => 860 - $positionWidthModifier, 'y' => 10, 'w' => 130 + $positionWidthModifier, 'h' => 80, 'color' => $tColor));
slantRectangle($mainBar, array('x' => 950, 'y' => 10, 'w' => 140, 'h' => 80, 'color' => "#303030"));
slantRectangle($mainBar, array('x' => 1050, 'y' => 10, 'w' => 160, 'h' => 80, 'color' => "white"));

slantRectangle($logoBox, array('x' => 10, 'y' => 10, 'w' => 160, 'h' => 80, 'color' => "white"));

$pPath = "teams/" . $row["team"] . "imgs/" . $row["first"] . $row["last"] . ".png";
$size = @getimagesize($pPath);

$nameModifier = 0;
$detailsModifier = 0;

$p = null;

if ($size[0]) {

	$p = array('w' => 192, 'h' => 230, 'x' => 400, 'y' => '801', 'path' => $pPath);

	if ($size[0] * 1.2 > $size[1]) {
		$p['h'] = $size[1] / ($size[0] / $p['w']);
		$p['y'] += 230 - $p['h'];
	}

	//placeImage($canvas, $p);
} else {
	$nameModifier = -150;
	$detailsModifier = -220;
}

placeImage($logoBox, array('x' => 52, 'y' => 12 - $boxHeightModifier, 'w' => 76, 'h' => 76, 'path' => "teamlogos/" . $teamrow["logo"]));
placeImage($mainBar, array('x' => 1092, 'y' => 12 - $boxHeightModifier, 'w' => 76, 'h' => 76, 'path' => "teamlogos/" . $teamrow["logo"]));

shadowText($mainBar, array('x' => 210 + $nameModifier, 'y' => 15, 'w' => 535 - $nameModifier - $positionWidthModifier, 'h' => 70, 'text' => $row["first"] . " " . $row["last"], 'gravity' => "west", 'font' => "fontN", 'color' => "white"));
shadowText($mainBar, array('x' => 750 - $positionWidthModifier, 'y' => 10, 'w' => 150, 'h' => 80, 'text' => $row["num"], 'gravity' => "center", 'font' => "fontN", 'color' => "white"));
shadowText($mainBar, array('x' => 860 - $positionWidthModifier, 'y' => 10, 'w' => 130 + $positionWidthModifier, 'h' => 80, 'text' => $row["pos"], 'gravity' => "center", 'font' => "fontN", 'color' => "white"));
shadowText($mainBar, array('x' => 950, 'y' => 10, 'w' => 140, 'h' => 80, 'text' => $row["year"], 'gravity' => "center", 'font' => "fontN", 'color' => "white"));

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
plainText($blackBox, array('x' => 280 + $detailsModifier, 'y' => 24, 'w' => 880 - $detailsModifier, 'h' => 33, 'text' => $details, 'gravity' => $detailsGravity, 'font' => "fontN", 'color' => "white"));

if ($stype && $stype != "txt") {
	if ($lastSeason == true) {
		shadowText($canvas, array('x' => 410, 'y' => 995, 'w' => 172, 'h' => 30, 'text' => 'Last Season:', 'gravity' => "center", 'font' => "fontN", 'color' => "white"));
	} else if ($row["team"] == career) {
		shadowText($canvas, array('x' => 410, 'y' => 995, 'w' => 172, 'h' => 30, 'text' => 'Career Stats:', 'gravity' => "center", 'font' => "fontN", 'color' => "white"));
	}
	$statsBoxWidth = 880;
	$statsBoxX = 260;
	if (!$size[0]) {
		$statsBoxX = 135;
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
		plainText($blackBox, array('x' => $statsBoxX, 'y' => 55, 'w' => $boxW, 'h' => 40, 'text' => $slabel[$j], 'gravity' => "center", 'font' => "fontN", 'color' => "white"));
		plainText($blackBox, array('x' => $statsBoxX, 'y' => 95, 'w' => $boxW, 'h' => 80, 'text' => $row[$j + 8], 'gravity' => "center", 'font' => "fontN", 'color' => "white"));
		$statsBoxX += ($boxW - $thisWidth) / 2 + $thisWidth + $spacing;
	}
}

/*$filename = $row["num"] . $row["first"] . $row["last"];

$mainBar->setImageDepth(8);
$mainBar->writeImage(realpath('anim') . '/' . $filename . '_bar.png');

$blackBox->setImageDepth(8);
$blackBox->writeImage(realpath('anim') . '/' . $filename . '_box.png');

$logoBox->setImageDepth(8);
$logoBox->writeImage(realpath('anim') . '/' . $filename . '_logo.png');

echo '<img src="anim/' . $filename . '_bar.png">';
echo '<img src="anim/' . $filename . '_box.png">';
echo '<img src="anim/' . $filename . '_logo.png">';*/


$frames1 = 30;

for($i = 0; $i < $frames1; $i++) {

	$file = '/' . $row["num"] . $row["first"] . $row["last"] . '_' . sprintf('%03d',$i) . '.png';

	$canvas = new Imagick();
	$canvas->newImage(1400, 260, "none", "png");

	$step = (1050-10)/($frames1-1)*$i + 10;

	$mainBarCopy = $mainBar->clone();
	$mainBarCopy->cropImage($step+40,110,0,0);
	$canvas->compositeImage($mainBarCopy, imagick::COMPOSITE_OVER, 10, 10);

	$canvas->compositeImage($logoBox, imagick::COMPOSITE_OVER, $step, 10);

	$canvas->writeImage(realpath('anim') . $file);
	echo $i . ':<br><img src="anim/' . $file . '"/ ><br>';
	flush();
}

$frames2 = 20;

for($i = 0; $i < $frames2; $i++) {
	$file = '/' . $row["num"] . $row["first"] . $row["last"] . '_' . sprintf('%03d',$frames1 + $i) . '.png';

	$step = (160)/($frames2-1)*$i ;

	$canvas = new Imagick();
	$canvas->newImage(1400, 260, "none", "png");
	$blackBoxCopy = $blackBox->clone();
	$blackBoxCopy->cropImage(1150,$step+10,0,170-$step);
	$canvas->compositeImage($blackBoxCopy, imagick::COMPOSITE_OVER, 50, 80);
	$canvas->compositeImage($mainBar, imagick::COMPOSITE_OVER, 10, 10);

	$canvas->writeImage(realpath('anim') . $file);
	echo $i . ':<br><img src="anim/' . $file . '"/ ><br>';
	flush();
}

if($p) {

	$headshot = new Imagick();
	$headshot->newImage(192,230, "none", "png");

	$p['x'] -= 400;
	$p['y'] -= 801;

	placeImage($headshot,$p);

	$frames3 = 20;

	for($i = 0; $i < $frames3; $i++) {
		$file = '/' . $row["num"] . $row["first"] . $row["last"] . '_' . sprintf('%03d',$frames1 + $frames2 + $i) . '.png';

		$step = (230)/($frames3-1)*$i ;

		$canvas = new Imagick();
		$canvas->newImage(1400, 260, "none", "png");
		$canvas->compositeImage($blackBox, imagick::COMPOSITE_OVER, 50, 80);
		$canvas->compositeImage($mainBar, imagick::COMPOSITE_OVER, 10, 10);

		$headshotCopy = $headshot->clone();
		$headshotCopy->cropImage(192,$step+1,0,0);
		$canvas->compositeImage($headshotCopy, imagick::COMPOSITE_OVER, 60, 230-$step+20);

		$canvas->writeImage(realpath('anim') . $file);
		echo $i . ', ' . $step . ':<br><img src="anim/' . $file . '"/ ><br>';
		flush();
	}
}

//$canvas->compositeImage($blackBox, imagick::COMPOSITE_OVER, 50, 90);

/*
$canvas->compositeImage($logoBox, imagick::COMPOSITE_OVER, 1050, 10);


header("Content-Type: image/png");
echo $canvas;*/


?>
