<?php

include_once('include.php');
include_once('imagick_include.php');

/*
 * lineHeight
 * bodyText
 * boxHeight
 * boxWidth
 * boxOffset
 * boxPadding
 *
 * titleColor
 * titleHeight
 * titleText
 *
 * subTitleColor
 * subTitleHeight
 * subTitleText
 * subTitleWidth
 *
 * logoHeight
 * logoLeft
 * logoRight
 */


function weather(&$canvas,$o,$bustCache = true) {

	$logoHeight = 0; $logoHeightDiff = 0; $logoXAdjust = 0;
	if(($o['logoLeft'] || $o['logoRight']) && $o['logoHeight'] > 0) {
		$logoHeight = $o['logoHeight'];
		$logoHeightDiff = ($logoHeight - $o['titleHeight']) / 2;
		$logoHeightDiff -= $o['subTitleHeight'];
		$logoHeightDiff = $logoHeightDiff > 0 ? $logoHeightDiff : 0;
		$logoXAdjust = $logoHeight*1.5;
	}

	$pad = 15;
	$bottom = 50;
	$geos = array();

	$text = $o['bodyText'];
	$text = str_replace('\n', PHP_EOL, $text);
	$lines = substr_count($text,PHP_EOL)+1;

	$textBoxHeight = ($o['boxHeight'] > 0) ? $o['boxHeight'] : $lines*$o['lineHeight'];
	$boxHeight = $textBoxHeight + $o['titleHeight'] + $o['subTitleHeight'] + $pad + $logoHeightDiff;

	$boxWidth = $o['boxWidth'];
	$boxX = ($o['boxOffset']>0) ? $o['boxOffset'] : (1920 - $boxWidth)/2;
	$boxY = 1080 - $boxHeight - $bottom;

	//
	// Main Black Box
	//

	$geos[] = array(
			'type'=>'blackBox',
			'x'=>$boxX,
			'y'=>$boxY	,
			'w'=>$boxWidth,
			'h'=>$boxHeight);

	//
	// Body Text
	//
	
	// Weather Getting
	$json_string = 'http://api.wunderground.com/api/9252b3b729b18d24/conditions/q/' . $o['ZipCode'] . '.json';

	
	$jsondata = file_get_contents($json_string);
	$obj = json_decode($jsondata, true);

	$weather_string = $obj["current_observation"]['weather'] . ' - ' . $obj["current_observation"]['temp_f'] . '°F' . ' - Wind: ' . $obj["current_observation"]['wind_mph'] . " MPH " . $obj["current_observation"]['wind_dir'] ;
	$weather_top = "Weather: " . $obj["current_observation"]['display_location']['full'] ;
	$geos[] = array(
			'type' => 'plainText',
			'font' => 'fontN',
			'gravity' => 'center',
			'text' => $weather_string,
			'color' => 'white',
			'x' => $boxX + $pad,
			'y' => 1080 - $textBoxHeight - $bottom,
			'w' => $boxWidth - $pad*2,
			'h' => $textBoxHeight,
			'wordWrap' => $lines > 1 ? false : true
	);

	//
	// Sub Title Bar
	//

	$subTitleWidth = $o['subTitleWidth'];
	if($subTitleWidth < 0) {
		$subTitleWidth = $boxWidth+$o['titleHeight'] + $o['subTitleWidth'];
	}
	$subTitleX = (1920-$subTitleWidth)/2;

	$geos[] = array(
			'type' => 'slantRectangle',
			'x' => $subTitleX,
			'y' => 1080 - $boxHeight - $bottom + $o['titleHeight'],
			'w' => $subTitleWidth,
			'h' => $o['subTitleHeight'],
			'color' => $o['subTitleColor']
	);

	//
	// Title Bar
	//

	$geos[] = array(
				'type' => 'slantRectangle',
				'x' => $boxX - $o['titleHeight']/2,
				'y' => 1080 - $boxHeight - $bottom,
				'w' => $boxWidth + $o['titleHeight'],
				'h' => $o['titleHeight'],
				'color' => $o['titleColor']
		);

	$barTextPad = 3;
	
	if($o['subTitleHeight'] > 0) {
		$geos[] = array(
				'type' => 'shadowText',
				'font' => 'fontN',
				'gravity' => 'center',
				'text' => $o['subTitleText'],
				'color' => 'white',
				'x' => $subTitleX + $o['subTitleHeight']/2,
				'y' => 1080 - $boxHeight - $bottom + $o['titleHeight'] + $barTextPad,
				'w' => $subTitleWidth - $o['subTitleHeight'],
				'h' => $o['subTitleHeight']-$barTextPad*2
		);
	}

	$titleXAdjust = 0;
	$titleWAdjust = 0;
	$logoY = 1080 - $boxHeight - $bottom - ($logoHeight-$o['titleHeight'])/2;

	//
	// Left Logo
	//
	
	$hour = date('H');
	$use_img = false;
	if (strpos($obj["current_observation"]['weather'], "Rain") !== false){
		$img_location = 'other_graphics/weather/rain.png';
		$use_img = true;
		}
		
	if (strpos($obj["current_observation"]['weather'], "Cloudy") !== false){
		$img_location = 'other_graphics/weather/cloudy.png';
		$use_img = true;
		}
		
	if (strpos($obj["current_observation"]['weather'], "Partly Cloudy") !== false){
		if($hour > 20 || $hour < 6){
			$img_location = 'other_graphics/weather/moon_cloud.png';
			}
		else{
			$img_location = 'other_graphics/weather/partly_cloudy.png';
			}
		$use_img = true;
		}
	
	if (strpos($obj["current_observation"]['weather'], "Clear") !== false){
		if($hour > 20 || $hour < 6){
			$img_location = 'other_graphics/weather/moon.png';
			}
		else{
			$img_location = 'other_graphics/weather/sunny.png';
			}
		$use_img = true;
		}
	
	if (strpos($obj["current_observation"]['weather'], "Sunny") !== false){
		$img_location = 'other_graphics/weather/sunny.png';
		$use_img = true;
		}
	
	if (strpos($obj["current_observation"]['weather'], "Snow") !== false){
		$img_location = 'other_graphics/weather/snow.png';
		$use_img = true;
		}
		
	if (strpos($obj["current_observation"]['weather'], "Thunder") !== false){
		$img_location = 'other_graphics/weather/thunder_storms.png';
		$use_img = true;
		}
	
	if (strpos($obj["current_observation"]['weather'], "Overcast") !== false){
		if($hour > 20 || $hour < 6){
			$img_location = 'other_graphics/weather/moon_cloud.png';
			}
		else{
			$img_location = 'other_graphics/weather/cloudy.png';
			}
		$use_img = true;
		}	
	
	if($o['logoLeft'] && $logoHeight && $use_img) {
		$geos[] = array(
				'type' => 'slantRectangle',
				'color' => 'white',
				'x' => $boxX - $logoHeight/2,
				'y' => $logoY,
				'w' => $logoHeight*2,
				'h' => $logoHeight
		);
		$geos[] = array(
				'type' => 'placeImage',
				'path' => $img_location,
				'x' => $boxX,
				'y' => $logoY,
				'w' => $logoHeight,
				'h' => $logoHeight
		);
		$titleXAdjust = $titleWAdjust = $logoXAdjust;
	}
	//
	// Right Logo
	//
	if($o['logoRight'] && $logoHeight) {
		$geos[] = array(
				'type' => 'slantRectangle',
				'color' => 'white',
				'x' => $boxX + $boxWidth - $logoHeight*1.5,
				'y' => $logoY,
				'w' => $logoHeight*2,
				'h' => $logoHeight
		);
		$geos[] = array(
				'type' => 'placeImage',
				'path' => $o['logoRight'],
				'x' => $boxX + $boxWidth - $logoHeight,
				'y' => $logoY,
				'w' => $logoHeight,
				'h' => $logoHeight
		);
		$titleWAdjust += $logoXAdjust;
	}





	$geos[] = array(
				'type' => 'shadowText',
				'font' => 'fontN',
				'gravity' => "left",
				'text' => $weather_top,
				'color' => 'white',
				'x' => $boxX + $titleXAdjust,
				'y' => 1080 - $boxHeight - $bottom + $barTextPad,
				'w' => $boxWidth - $titleWAdjust,
				'h' => $o['titleHeight']-$barTextPad*2
		);

	//echo '<pre>'; print_r($geos);

	foreach($geos as $geo) {
		addGeoToCanvas($canvas, $geo);
	}

	//return $localCanvas;
}

?>
