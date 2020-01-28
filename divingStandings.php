<?php

function divingStandings(&$canvas, $geo) {
	$geo['w'] = 1200;
	$geo['x'] = 360;
	$sort = $geo['sort'];
	$team = $geo['team'];
	$limit = $geo['limit'];
	$offset = $geo['offset'];
	$dir = $geo['sortDirection'];

	$sort = $geo['sort'];
	if($geo['numericalSort'] == 'true') {
		$sort = '(0 + ' . $sort . ')';
	}

	if ($team == 'ecac'){
	$team = "rpi-mh,dart-mh,yale-mh,union-mh,clark-mh,brown-mh,colgate-mh,quin-mh,stl-mh,prin-mh,cornell-mh,harvard-mh";
	$teamarray = explode(',', $team);
	$finalteamstring = "";
	foreach($teamarray as $strval){
		$finalteamstring = $finalteamstring . "`team`='" . trim($strval) . "' OR ";
	}
	$finalteamstring = substr($finalteamstring, 0, -4);
	$sql = "SELECT * FROM `players` WHERE NOT `pos`='G' AND (" . $finalteamstring . ") ORDER BY " . $sort . " " . $dir . " LIMIT " . $offset . ','. $limit;
	}
	else if(strpos($team, ',') !== false){
	$teamarray = explode(',', $team);
	$finalteamstring = "";
	foreach($teamarray as $strval){
		$finalteamstring = $finalteamstring . "`team`='" . trim($strval) . "' OR ";
	}
		$finalteamstring = substr($finalteamstring, 0, -4);
		$sql = "SELECT * FROM `players` WHERE NOT `pos`='G' AND (" . $finalteamstring . ") ORDER BY " . $sort . " " . $dir . " LIMIT " . $offset . ','. $limit;
	}
	else{
	$sql = "SELECT * FROM players WHERE `team`='" . $team . "' ORDER BY " . $sort . " " . $dir . " LIMIT " . $offset . ','. $limit;
	}

	$result = dbQuery($sql);

	$players = [];

	while($row = rpits_db_fetch_assoc($result)) {
		$players[] = $row;
	}

	$rowHeight = 60;

	$height = count($players) * $rowHeight;
	$yLocation = 1080 - $height - 50;

	$yOffset = $yLocation;
	blackBox($canvas,array('x'=>$geo['x'],'w'=>$geo['w'],'y'=>$yLocation-65,'h'=>$height+65));

	slantRectangle($canvas,array('x'=>$geo['x']+200,'y'=>$yLocation-60,'w'=>800,'h'=>50,'color'=>'#333333'));
	shadowText($canvas,array('x'=>$geo['x']+220,'y'=>$yLocation-55,'gravity'=>'center','w'=>760,'h'=>40,'text'=>$geo['roundText'],'font'=>'fontN','color'=>'white'));

	slantRectangle($canvas,array('x'=>$geo['x']-35,'y'=>$yLocation-130,'w'=>1270,'h'=>70,'color'=>'red'));
	shadowText($canvas,array('x'=>$geo['x'],'y'=>$yLocation-125,'gravity'=>'center','w'=>1200,'h'=>60,'text'=>$geo['titleText'],'font'=>'fontN','color'=>'white'));

	shadowText($canvas,array('x'=>$geo['x']+1030,'y'=>$yLocation-50,'gravity'=>'east','w'=>150,'h'=>50,'text'=>$geo['labelText'],'font'=>'fontN','color'=>'white'));
	

	$place = $offset;

	foreach($players as $player) {
		$place++;
		$team = array();
		if($player['stype'] == 'dive') {
			$team = fetchOrg($player['pos']);
		} else {
			$team = fetchTeam($player['team']);
		}
		if($geo['hideRankColumn'] == 'false') {
			shadowText($canvas,array('x'=>$geo['x'],'y'=>$yOffset,'gravity'=>'east','w'=>65,'h'=>$rowHeight,'text'=>$place,'font'=>'fontN','color'=>'white'));
		}
		placeImage($canvas,array('x'=>$geo['x']+75,'y'=>$yOffset,'w'=>65,'h'=>$rowHeight,'path'=>$team['logo']));
		shadowText($canvas,array('x'=>$geo['x']+150,'y'=>$yOffset,'gravity'=>'west','w'=>425,'h'=>$rowHeight,'text'=>$player['first'] . ' ' . $player['last'],'font'=>'fontN','color'=>'white'));
		shadowText($canvas,array('x'=>$geo['x']+585,'y'=>$yOffset+4,'gravity'=>'west','w'=>485,'h'=>$rowHeight-8,'text'=>$team['name'],'font'=>'fontN','color'=>'white'));
		if($geo['hideLastColumn'] == 'false') {
			shadowText($canvas,array('x'=>$geo['x']+1080,'y'=>$yOffset,'gravity'=>'east','w'=>100,'h'=>$rowHeight,'text'=>$player[$geo['sort']],'font'=>'fontN','color'=>'white'));
		}
		$yOffset += $rowHeight;
	}
}

?>
