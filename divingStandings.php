<?php

function divingStandings(&$canvas, $geo) {
	$geo['w'] = 1200;
	$geo['x'] = 360;
	$sort = $geo['sort'];
	$team = $geo['team'];
	$limit = $geo['limit'];
	$sql = "SELECT * FROM players WHERE `team`='" . $team . "' ORDER BY (0 + " . $sort . ") DESC LIMIT " . $limit;

	$result = dbQuery($sql);

	$players = [];

	while($row = mysql_fetch_assoc($result)) {
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
	

	$place = 0;

	foreach($players as $player) {
		$place++;
		$team = array();
		if($player['stype'] == 'dive') {
			$team = fetchOrg($player['pos']);
		} else {
			$team = fetchTeam($player['team']);
		}
		shadowText($canvas,array('x'=>$geo['x'],'y'=>$yOffset,'gravity'=>'east','w'=>65,'h'=>$rowHeight,'text'=>$place,'font'=>'fontN','color'=>'white'));
		placeImage($canvas,array('x'=>$geo['x']+75,'y'=>$yOffset,'w'=>65,'h'=>$rowHeight,'path'=>$team['logo']));
		shadowText($canvas,array('x'=>$geo['x']+150,'y'=>$yOffset,'gravity'=>'west','w'=>450,'h'=>$rowHeight,'text'=>$player['first'] . ' ' . $player['last'],'font'=>'fontN','color'=>'white'));
		shadowText($canvas,array('x'=>$geo['x']+610,'y'=>$yOffset,'gravity'=>'west','w'=>560,'h'=>$rowHeight,'text'=>$team['name'],'font'=>'fontN','color'=>'white'));
		shadowText($canvas,array('x'=>$geo['x']+1080,'y'=>$yOffset,'gravity'=>'east','w'=>100,'h'=>$rowHeight,'text'=>$player[$geo['sort']],'font'=>'fontN','color'=>'white'));
		$yOffset += $rowHeight;
	}
	
	//print_r($players);



}

?>
