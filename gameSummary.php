<?php

function periodLabel($period) {
	$width = 980;
	$height = 50;
	$label = new Imagick();
	$label->newPseudoImage($width, $height, 'xc:none');

	$periods = array(
			1=>'1st Period',
			2=>'2nd Period',
			3=>'3rd Period',
			4=>'Overtime'
	);

	plainText($label, array(
			'text'=>$periods[$period+0],
			'w'=>$width,
			'h'=>44,
			'x'=>0,
			'y'=>3,
			'gravity'=>'center',
			'font'=>'fontN',
			'color'=>'white'
	));

	return $label;

}

function scoringPlay($score,$teams) {
	$width = 980;
	$height = 100;
	$play = new Imagick();
	$play->newPseudoImage($width, $height, 'xc:none');

	placeImage($play,array(
			'w'=>94,
			'h'=>94,
			'x'=>3,
			'y'=>3,
			'path'=>$teams[$score["vh"].'']['logo']
	));

	$goal = explode(',',$score['name']);
	$a1 = explode(',',$score['assist1']);
	$a2 = explode(',',$score['assist2']);

	$goalString = ucwords(strtolower($goal[0]));
	if(strlen($score['seasong'])) {
		$goalString .= ' (' . $score['seasong'] . ')';
	}

	plainText($play,array(
		'text'=>$goalString,
			'w'=>500,
			'h'=>45,
			'x'=>125,
			'y'=>10,
			'gravity'=>'west',
			'font'=>'fontN',
			'color'=>'white'
	));

	$assistString = 'Unassisted';

	if($a1[0]) {
		$assistString = ucwords(strtolower($a1[0]));
		if(strlen($score['seasona1'])) {
			$assistString .= ' (' . $score['seasona1'] . ')';
		}
	}
	if($a2[0]) {
		$assistString .= ', ' . ucwords(strtolower($a2[0]));
		if(strlen($score['seasona2'])) {
			$assistString .= ' (' . $score['seasona2'] . ')';
		}
	}
	plainText($play,array(
		'text'=>$assistString,
			'w'=>500,
			'h'=>40,
			'x'=>125,
			'y'=>55,
			'gravity'=>'west',
			'font'=>'fontN',
			'color'=>'white'
	));

	$time = $score['time'] . '';
	if($time[0] == '0') {
		$time = substr($time,1,strlen($time));
	}
	//$time = str_replace('0','',$time);

	plainText($play,array(
		'text'=>$time,
			'w'=>150,
			'h'=>50,
			'x'=>825,
			'y'=>25,
			'gravity'=>'center',
			'font'=>'fontN',
			'color'=>'white'
	));

	$strength = $score['type'] != 'EV' ? $score['type'] : '';

	plainText($play,array(
		'text'=>$strength,
			'w'=>80,
			'h'=>50,
			'x'=>725,
			'y'=>25,
			'gravity'=>'center',
			'font'=>'fontN',
			'color'=>'white'
	));


	return $play;
}

function gameSummary(&$canvas,$o) {

	$maxHeight = 910;

	$width = 980;
	$height = $maxHeight;
	$plays = new Imagick();
	$plays->newPseudoImage($width, $height, 'xc:none');

	$teams = array();
	$teams['V'] = fetchTeam($o['vTeam']);
	$teams['H'] = fetchTeam($o['hTeam']);

	$bodyHeight = 200;

	$path = "http://www.sidearmstats.com/rpi/mhockey/1.xml";
	$path = "sidearm.xml";
	$path = $o['liveStatsXML'];

	$scores = getGameSummaryInfo($path);
	$vOffset = 0;
	$lastPeriod = 0;
	foreach($scores->score as $score) {
		if($vOffset+150 > $maxHeight) break;
		if($vOffset > 0) {
			$plays->compositeImage(fillRectangle(960,2,'#FFF'),imagick::COMPOSITE_OVER,0,$vOffset);
		}
		if($score['prd']+0 != $lastPeriod) {
			$lastPeriod = $score['prd']+0;
			$plays->compositeImage(periodLabel($lastPeriod),imagick::COMPOSITE_OVER,0,$vOffset);
			$vOffset += 50;
			$plays->compositeImage(fillRectangle(960,2,'#FFF'),imagick::COMPOSITE_OVER,0,$vOffset);
		}
		$plays->compositeImage(scoringPlay($score,$teams),imagick::COMPOSITE_OVER,0,$vOffset);
		$vOffset += 100;
		//print_r($score);
	}
	

	addGeoToCanvas($canvas,array(
			'type'=>'flexBox',
			'w'=>1920,
			'h'=>1080,
			'x'=>0,
			'y'=>0,

			'bodyText'=>'',
			'boxHeight'=>$vOffset,
			'boxWidth'=>1000,
			'boxOffset'=>'auto',
			'boxPadding'=>10,

			'titleColor'=>$o['barColor'],
			'titleHeight'=>'70',
			'titleText'=>$o['titleText'],
			'titleGravity'=>'center',

			'logoHeight'=>100,
			'logoLeft'=>$teams['H']['logo'],
			'logoRight'=>$teams['V']['logo']
	));

	$canvas->compositeImage($plays,imagick::COMPOSITE_OVER,(1920-980)/2,1080-50-$vOffset-10);

}

function getGameSummaryInfo($path) {
	$page = fopen($path,"r");
	$contents = stream_get_contents($page);

	$sidearm = new SimpleXMLElement($contents);

	return $sidearm->scores;
}
?>
