<?php

include_once("include.php");
include_once("imagick_include.php");

function getStatscard($id,$o = []) {

	$lastSeason = false; // sets last season flag

	timestamp ('Get Statscard');

	//
	// Gather required data
	//

	$result = dbquery("SELECT * from players WHERE `id` = '$id'");
	$row = rpits_db_fetch_assoc($result);

	$team = fetchTeam($row['team']);

	$stype = $row["stype"];
	if ($stype != "txt") {
		$result = dbquery("SELECT * FROM stattype WHERE `type`  = '$stype'");
		$slabel = rpits_db_fetch_array($result);
	}

	timestamp ('Done getting data');

	$title = $row;
	$title['type'] = 'player';

	$geos = array();

	//
	// Set up geometry
	//

	$boxHeightModifier = 0;
	$positionWidth = 130;

	// Check to see if there are stats
	if (!$stype) {
		$boxHeightModifier = -113;
	}

	if ($stype == 'dive' && $row['s2'] > 0) {
		$boxHeightModifier = -50;
	} else if ($stype == 'dive') {
		$boxHeightModifier = -113;
	}

	// Adjust position width
	if ($stype != dive) {
		if (strlen($row["pos"])>1) {
			$positionWidth = 110 + ( .75 * getTextWidthFromCache(array('w' => 1000, 'h' => 80, 'text' => $row['pos'], 'font' => "fontN")) );
		}
	}

	$nameBarAdjust = 0;

	$diveTeam = [];

	if($stype == 'dive') {
		$diveTeam = fetchOrg($row['pos']);
		//print_R($diveTeam);
		$team['logo'] = $diveTeam['logo'];
		$team['color'] = $diveTeam['color'];
		$nameBarAdjust = 200;
	}

	//
	// Lay down initial geometry
	//

	$geos[] = array('type' => 'blackBox', 'name' => 'Backdrop', 'x' => 400, 'y' => 870 - $boxHeightModifier, 'w' => 1120, 'h' => 160 + $boxHeightModifier);

	$geos[] = array('type' => 'slantRectangle', 'name' => 'nameBar', 'x' => 360, 'y' => 800 - $boxHeightModifier, 'w' => 780 + $nameBarAdjust, 'h' => 80, 'color' => $team['color']);

	if ($stype != 'dive') {
		$geos[] = array('type' => 'slantRectangle', 'name' => 'numberBox', 'x' => 1230 - $positionWidth, 'y' => 800 - $boxHeightModifier, 'w' => 150, 'h' => 80, 'color' => "#303030");
		$geos[] = array('type' => 'slantRectangle', 'name' => 'positionBox', 'x' => 1340 - $positionWidth, 'y' => 800 - $boxHeightModifier, 'w' => $positionWidth, 'h' => 80, 'color' => $team['color']);
	}

	$geos[] = array('type' => 'slantRectangle', 'name' => 'yearBox', 'x' => 1300, 'y' => 800 - $boxHeightModifier, 'w' => 150, 'h' => 80, 'color' => "#303030");
	$geos[] = array('type' => 'slantRectangle', 'name' => 'logoBox', 'x' => 1410, 'y' => 800 - $boxHeightModifier, 'w' => 150, 'h' => 80, 'color' => "white");

	//
	// Headshot setup
	//

	$pPath = "teams/" . $row["team"] . "imgs/" . $row["first"] . $row["last"] . ".png";
	$size = @getimagesize($pPath);
	$nameModifier = 0;
	$detailsModifier = 0;

	$useHeadshot = !!$size[0] || $o['emptyHeadshot'];

	if ($useHeadshot) { // there is a headshot
		if($size[0] && !$o['emptyHeadshot']) {
			if ($stype) {
				$p = array('type' => 'placeHeadshot', 'name' => 'headshot', 'w' => 192, 'h' => 230, 'x' => 400, 'y' => '801', 'path' => $pPath);
			} else {
				$p = array('type' => 'placeHeadshot', 'name' => 'headshot', 'w' => 192, 'h' => 230, 'x' => 400, 'y' => '801', 'shadow' => 5, 'path' => $pPath);
			}

			// center align title and send to baseline if title is too narrow
			if ($size[0] * 1.2 > $size[1]) {
				$p['h'] = $size[1] / ($size[0] / $p['w']);
				$p['y'] += 230 - $p['h'];
			}

			$geos[] = $p;
		}

		if ($stype == 'dive') {
			if ($row['s2'] > 0) {
				$nameModifier = 15;
			} else {
				$nameModifier = 45;
			}
		}
		
		$nameModifier = 40;

	} else { // no headshot
		$nameModifier = -150;
		$detailsModifier = -220;
	}

	//
	// Print team logo, name, num, pos, year
	//

	$proSpacer = 0;
	if ($row['s7'] and ($stype != 'dive')) {  // draft pick graphic
		$geos[] = array('type' => 'placeImage', 'name' => 'draftLogo', 'x' => 1160 - $positionWidth, 'y' => 800 - $boxHeightModifier, 'w' => 76, 'h' => 76, 'shadow' => 5, 'padding' => 7, 'path' => 'other_graphics/NHL/'.strtoupper($row['s7']).'.png');
		$proSpacer = 55;
	}
	$geos[] = array('type' => 'placeImage', 'name' => 'teamLogo', 'x' => 1447, 'y' => 800 - $boxHeightModifier, 'w' => 76, 'h' => 76, 'path' => $team['logo'], 'shadow' => 5, 'padding' => 6);
	$geos[] = array('type' => 'shadowText', 'name' => 'name', 'x' => 560 + $nameModifier, 'y' => 805 - $boxHeightModifier, 'w' => 665 - $nameModifier - $positionWidth - $proSpacer, 'h' => 70, 'text' => $row["first"] . " " . $row["last"], 'gravity' => "west", 'font' => "fontN", 'color' => "white");

	if ($stype != 'dive') {
		$geos[] = array('type' => 'shadowText', 'name' => 'number', 'x' => 1230 - $positionWidth, 'y' => 803 - $boxHeightModifier, 'w' => 150, 'h' => 80, 'text' => $row["num"], 'gravity' => "center", 'font' => "fontN", 'color' => "white");
		$geos[] = array('type' => 'shadowText', 'name' => 'position', 'x' => 1338 - $positionWidth, 'y' => 803 - $boxHeightModifier, 'w' => $positionWidth, 'h' => 80, 'text' => $row["pos"], 'gravity' => "center", 'font' => "fontN", 'color' => "white");
	}

	$geos[] = array('type' => 'shadowText', 'name' => 'year', 'x' => 1305, 'y' => 803 - $boxHeightModifier, 'w' => 140, 'h' => 80, 'text' => $row["year"], 'gravity' => "center", 'font' => "fontN", 'color' => "white");

	//
	// Details setup
	//

	$details = '';

	if ($stype == 'dive') {
		$details = 'School: ' . $diveTeam['name'] . '       ';
	}

	if (trim($row["hometown"]) != "") {
		$details .= "Hometown: " . $row["hometown"];
	}

	if ($row["height"].length > 0) {
		$details .= "       " . "Ht: " . $row["height"];

	}

	if ($row["weight"] . length > 0) {
		$details .=  "        " . "Wt: " . $row["weight"];
	}
	$detailsGravity = "west";

	if (!$useHeadshot) {
		$detailsGravity = "center";
	}

	$geos[] = array('type' => 'plainText', 'name' => 'details', 'x' => 630 + $detailsModifier, 'y' => 884 - $boxHeightModifier, 'w' => 880 - $detailsModifier, 'h' => 33, 'text' => $details, 'gravity' => $detailsGravity, 'font' => "fontN", 'color' => "white");

	//
	// Stats section
	//

	if ($stype && $stype != "txt" && $stype != 'dive') {
		if ($lastSeason == true && !$useHeadshot) {
			$geos[] = array('type' => 'plainText', 'name' => 'lastSeason', 'x' => 420, 'y' => 965, 'w' => 80, 'h' => 60, 'text' => 'Last\nSeason:', 'gravity' => "west", 'font' => "fontN", 'color' => "white");
		} else if ($lastSeason == true && !$o['emptyHeadshot']) {
			$geos[] = array('type' => 'shadowText', 'name' => 'lastSeason', 'x' => 410, 'y' => 995, 'w' => 172, 'h' => 30, 'text' => 'Last Season:', 'gravity' => "center", 'font' => "fontN", 'color' => "white");
		} else if ($row["team"] == career) {
			$geos[] = array('type' => 'shadowText', 'name' => 'careerStats', 'x' => 410, 'y' => 995, 'w' => 172, 'h' => 30, 'text' => 'Career Stats:', 'gravity' => "center", 'font' => "fontN", 'color' => "white");
		}
		$statsBoxWidth = 880;
		$statsBoxX = 650;
		if (!$useHeadshot) {
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
	} else if ($stype == 'dive' && $row['s2'] > 0) {

		$centerMod = 0;
		if (!$useHeadshot) {
			$centerMod = -110;
		}

		$numY = 970;
		$labelY = 985;

		$roundPos = 585; // 150 w
		$roundNumPos = 875; // 100 w?

		$totalPos = 835; // 250 w
		$totalNumPos = 1225; // ~100w

		if($row['s1'] && $row['s1'] > 0) {
			$roundPos = 489; // 150 w
			$roundNumPos = 764; // 100 w?

			$scorePos = 695;
			$scoreNumPos = 970;

			$totalPos = 960; // 250 w
			$totalNumPos = 1335; // ~100w

			$geos[] = array('type' => 'shadowText', 'name' => 'score', 'x' => $scorePos+$centerMod, 'y' => $labelY, 'w' => 250, 'h' => 40, 'text' => 'Score', 'gravity' => "east", 'font' => "fontN", 'color' => "white");
			$geos[] = array('type' => 'shadowText', 'name' => 'scoreNumber', 'x' => $scoreNumPos+$centerMod, 'y' => $numY, 'w' => 300, 'h' => 60, 'text' => $row['s1'], 'gravity' => "west", 'font' => "fontN", 'color' => "white");
		}
		$geos[] = array('type' => 'shadowText', 'name' => 'round', 'x' => $roundPos+$centerMod, 'y' => $labelY, 'w' => 250, 'h' => 40, 'text' => 'Round', 'gravity' => "east", 'font' => "fontN", 'color' => "white");
		$geos[] = array('type' => 'shadowText', 'name' => 'roundNumber', 'x' => $roundNumPos+$centerMod, 'y' => $numY, 'w' => 300, 'h' => 60, 'text' => $team['chn_id'], 'gravity' => "west", 'font' => "fontN", 'color' => "white");

		$geos[] = array('type' => 'shadowText', 'name' => 'totalScore', 'x' => $totalPos+$centerMod, 'y' => $labelY, 'w' => 350, 'h' => 40, 'text' => 'Total Score', 'gravity' => "east", 'font' => "fontN", 'color' => "white");
		$geos[] = array('type' => 'shadowText', 'name' => 'scoreAmount', 'x' => $totalNumPos+$centerMod, 'y' => $numY, 'w' => 300, 'h' => 60, 'text' => $row['s2'], 'gravity' => "west", 'font' => "fontN", 'color' => "white");
		/*$row['s1'];
		$row['s2'];
		$row['s3'];
		$row['s4'];*/
	}

	$title['geos'] = $geos;

	timestamp ('Done Getting Statscard');

	return $title;
}

?>
