<?php

include_once("include.php");
include_once("imagick_include.php");

function getStatscard($id) {

	$lastSeason = true;

	timestamp ('Get Statscard');

	//
	// Gather required data
	//

	mysql_select_db("rpihockey"); // TODO: Migrate all tables over to rpits

	$result = dbquery("SELECT * from players WHERE `id` = '$id'");
	$row = mysql_fetch_assoc($result);

	$result = dbquery("SELECT * from teams WHERE `name` = '" . $row["team"] . "'");
	$teamrow = mysql_fetch_array($result);
	$tColor = rgbhex($teamrow["colorr"], $teamrow["colorg"], $teamrow["colorb"]);

	$stype = $row["stype"];
	if ($stype != "txt") {
		$result = dbquery("SELECT * FROM stattype WHERE `type`  = '$stype'");
		$slabel = mysql_fetch_array($result);
	}

	timestamp ('Done getting data');

	$title = $row;
	$title['type'] = 'player';

	$geos = array();

	//
	// Set up geometry
	//

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

	//
	// Lay down initial geometry
	//

	$geos[] = array('type' => 'blackBox', 'name' => 'Backdrop', 'x' => 400, 'y' => 870 - $boxHeightModifier, 'w' => 1120, 'h' => 160 + $boxHeightModifier);

	$geos[] = array('type' => 'slantRectangle', 'name' => 'nameBar', 'x' => 360, 'y' => 800 - $boxHeightModifier, 'w' => 780, 'h' => 80, 'color' => $tColor);
	$geos[] = array('type' => 'slantRectangle', 'name' => 'numberBox', 'x' => 1100 - $positionWidthModifier, 'y' => 800 - $boxHeightModifier, 'w' => 150, 'h' => 80, 'color' => "#303030");
	$geos[] = array('type' => 'slantRectangle', 'name' => 'positionBox', 'x' => 1210 - $positionWidthModifier, 'y' => 800 - $boxHeightModifier, 'w' => 130 + $positionWidthModifier, 'h' => 80, 'color' => $tColor);
	$geos[] = array('type' => 'slantRectangle', 'name' => 'yearBox', 'x' => 1300, 'y' => 800 - $boxHeightModifier, 'w' => 140, 'h' => 80, 'color' => "#303030");
	$geos[] = array('type' => 'slantRectangle', 'name' => 'logoBox', 'x' => 1400, 'y' => 800 - $boxHeightModifier, 'w' => 160, 'h' => 80, 'color' => "white");

	//
	// Headshot setup
	//

	$pPath = "teams/" . $row["team"] . "imgs/" . $row["first"] . $row["last"] . ".png";
	$size = @getimagesize($pPath);
	$nameModifier = 0;
	$detailsModifier = 0;

	if ($size[0]) {
		// there is a headshot
		$p = array('type' => 'placeImage', 'name' => 'headshot', 'w' => 192, 'h' => 230, 'x' => 400, 'y' => '801', 'path' => $pPath);

		// center align title and send to baseline if title is too narrow
		if ($size[0] * 1.2 > $size[1]) {
			$p['h'] = $size[1] / ($size[0] / $p['w']);
			$p['y'] += 230 - $p['h'];
		}

		$geos[] = $p;
	} else {
		$nameModifier = -150;
		$detailsModifier = -220;
	}

	//
	// Print team logo, name, num, pos, year
	//

	$geos[] = array('type' => 'placeImage', 'name' => 'teamLogo', 'x' => 1442, 'y' => 802 - $boxHeightModifier, 'w' => 76, 'h' => 76, 'path' => "teamlogos/" . $teamrow["logo"]);

	$geos[] = array('type' => 'shadowText', 'name' => 'name', 'x' => 560 + $nameModifier, 'y' => 805 - $boxHeightModifier, 'w' => 535 - $nameModifier - $positionWidthModifier, 'h' => 70, 'text' => $row["first"] . " " . $row["last"], 'gravity' => "west", 'font' => "fontN", 'color' => "white");
	$geos[] = array('type' => 'shadowText', 'name' => 'number', 'x' => 1100 - $positionWidthModifier, 'y' => 800 - $boxHeightModifier, 'w' => 150, 'h' => 80, 'text' => $row["num"], 'gravity' => "center", 'font' => "fontN", 'color' => "white");
	$geos[] = array('type' => 'shadowText', 'name' => 'position', 'x' => 1210 - $positionWidthModifier, 'y' => 800 - $boxHeightModifier, 'w' => 130 + $positionWidthModifier, 'h' => 80, 'text' => $row["pos"], 'gravity' => "center", 'font' => "fontN", 'color' => "white");
	$geos[] = array('type' => 'shadowText', 'name' => 'year', 'x' => 1300, 'y' => 800 - $boxHeightModifier, 'w' => 140, 'h' => 80, 'text' => $row["year"], 'gravity' => "center", 'font' => "fontN", 'color' => "white");

	//
	// Details setup
	//

	$details = "Hometown: " . $row["hometown"] . "       Ht: " . $row["height"];
	if ($row["weight"] . length > 0) {
		$details .= "       Wt: " . $row["weight"];
	}
	$detailsGravity = "west";

	if (!$size[0]) {
		$details = "Hometown: " . $row["hometown"] . "       Height: " . $row["height"];
		if ($row["weight"] . length > 0) {
			$details .= "       Weight: " . $row["weight"];
		}
		$detailsGravity = "center";
	}

	$geos[] = array('type' => 'plainText', 'name' => 'details', 'x' => 630 + $detailsModifier, 'y' => 884 - $boxHeightModifier, 'w' => 880 - $detailsModifier, 'h' => 33, 'text' => $details, 'gravity' => $detailsGravity, 'font' => "fontN", 'color' => "white");

	//
	// Stats section
	//

	if ($stype && $stype != "txt") {
		if ($lastSeason == true && !$size[0]) {
			$geos[] = array('type' => 'plainText', 'name' => 'lastSeason', 'x' => 420, 'y' => 965, 'w' => 80, 'h' => 60, 'text' => 'Last\nSeason:', 'gravity' => "west", 'font' => "fontN", 'color' => "white");
		}	else if ($lastSeason == true) {
			$geos[] = array('type' => 'shadowText', 'name' => 'lastSeason', 'x' => 410, 'y' => 995, 'w' => 172, 'h' => 30, 'text' => 'Last Season:', 'gravity' => "center", 'font' => "fontN", 'color' => "white");
		} else if ($row["team"] == career) {
			$geos[] = array('type' => 'shadowText', 'name' => 'careerStats', 'x' => 410, 'y' => 995, 'w' => 172, 'h' => 30, 'text' => 'Career Stats:', 'gravity' => "center", 'font' => "fontN", 'color' => "white");
		}
		$statsBoxWidth = 880;
		$statsBoxX = 650;
		if (!$size[0]) {
			$statsBoxX = 525;
			$statsBoxWidth = 950;
		}

		$i = 1;
		for (; strlen($slabel['l'.$i]) > 0; $i++) {	}

		timestamp ('Calculating widths...');

		$boxW = $statsBoxWidth / ($i-1);

		$totalwidths = 0;
		for ($j = 1; $j < $i; $j++) {
			$totalwidths += getTextWidthFromCache(array('w' => $boxW, 'h' => 80, 'text' => $row['s'.$j], 'font' => "fontN"));
			timestamp ('getTextWidth' . $j);
		}

		$spacing = ($statsBoxWidth - $totalwidths) / ($i-1);

		for ($j = 1; $j < $i; $j++) {
			$thisWidth = getTextWidthFromCache(array('w' => $boxW, 'h' => 80, 'text' => $row['s'.$j], 'font' => "fontN"));
			timestamp ('getTextWidth' . $j);
			$statsBoxX -=($boxW - $thisWidth) / 2;
			$geos[] = array('type' => 'plainText', 'name' => 'stat' . $j . 'Label', 'x' => $statsBoxX, 'y' => 915, 'w' => $boxW, 'h' => 40, 'text' => $slabel['l'.$j], 'gravity' => "center", 'font' => "fontN", 'color' => "white");
			$geos[] = array('type' => 'plainText', 'name' => 'stat' . $j . 'Value', 'x' => $statsBoxX, 'y' => 955, 'w' => $boxW, 'h' => 80, 'text' => $row['s'.$j], 'gravity' => "center", 'font' => "fontN", 'color' => "white");
			$statsBoxX += ($boxW - $thisWidth) / 2 + $thisWidth + $spacing;
		}
	}

	$title['geos'] = $geos;

	timestamp ('Done Getting Statscard');

	return $title;
}

?>
