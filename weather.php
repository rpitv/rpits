<?php

include_once('include.php');
include_once('imagick_include.php');

function weather(&$canvas,$o,$bustCache = true) {

	$logoHeight = 0; $logoHeightDiff = 0; $logoXAdjust = 0;
	if (($o['logoLeft'] || $o['logoRight']) && $o['logoHeight'] > 0) {
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

	// Main Black Box

	$geos[] = array(
			'type'=>'blackBox',
			'x'=>$boxX,
			'y'=>$boxY,
			'w'=>$boxWidth,
			'h'=>$boxHeight);

	// Body Text

	// Get weather via wunderground api
	$json_url = 'http://api.wunderground.com/api/9252b3b729b18d24/conditions/astronomy/q/' . $o['ZipCode'] . '.json';
	$json_data = file_get_contents($json_url);
	$obj = json_decode($json_data, true);

	$windspeed = (int)$obj["current_observation"]['wind_mph'];
	$temperature = (int)$obj["current_observation"]['temp_f'];

	$wind_string = "";

	if ($windspeed == 0){
		$wind_string = " Wind: Calm";
	}
	else{
		$wind_string = " Wind: " . $windspeed . " MPH " . $obj["current_observation"]['wind_dir'];
	}

	$weather_string = $obj["current_observation"]['weather'] . ' ' . $temperature . '°F' . ' -' . $wind_string ;
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
			'h' => $textBoxHeight
	);

	// Sub Title Bar

	$subTitleWidth = $o['subTitleWidth'];
	if ($subTitleWidth < 0) {
		$subTitleWidth = $boxWidth+$o['titleHeight'] + $o['subTitleWidth'];
	}
	$subTitleX = (1920-$subTitleWidth)/2;

	$geos[] = array(
			'type' => 'slantRectangle',
			'x' => $subTitleX + $pad,
			'y' => 1080 - $boxHeight - $bottom + $o['titleHeight'],
			'w' => $subTitleWidth,
			'h' => $o['subTitleHeight'],
			'color' => $o['subTitleColor']
	);

	// Title Bar

	$geos[] = array(
				'type' => 'slantRectangle',
				'x' => $boxX - $o['titleHeight']/2,
				'y' => 1080 - $boxHeight - $bottom - 3,
				'w' => $boxWidth + $o['titleHeight'],
				'h' => $o['titleHeight'],
				'color' => $o['titleColor']
		);

	$barTextPad = 3;

	if ($o['subTitleHeight'] > 0) {
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

	// Left Logo

	//pull date from data stream for reliability else get server time
	//time in minutes
	if (isset($obj['moon_phase']['current_time'])) {
		$current_time = ($obj['moon_phase']['current_time']['hour'])*60 + $obj['moon_phase']['current_time']['minute'];
	} else {
		$current_time = date('H')*60 + date('i');
	}
	//sunrise/sunset times
	//30 minutes adjustments account for when it is bright
	if (isset($obj['sun_phase'])) {
		$sunrise = ($obj['sun_phase']['sunrise']['hour']) * 60 + $obj['sun_phase']['sunrise']['minute'] - 30;
		$sunset = ($obj['sun_phase']['sunset']['hour']) * 60 + $obj['sun_phase']['sunset']['minute'] + 30;
	} else {
		$sunrise = 7 * 60 - 30;
		$sunset = 20 * 60 + 30;
	}
	//choose images based on available imageset
	$weather_images = array(
		'Sunny' => 'clear.png',
		'Clear' => 'clear.png',
		'Scattered Clouds' => 'partly_cloudy.png',
		'Partly Cloudy' => 'partly_cloudy.png',
		'Mostly Cloudy' => 'partly_cloudy.png',
		'Overcast' => 'cloudy.png',
		'Cloudy' => 'cloudy.png',
		'Light Rain' => 'rain.png',
		'Heavy Rain' => 'rain.png',
		'Rain' => 'rain.png',
		'Rain Showers' => 'rain.png',
		'Rain Mist' => 'rain.png',
		'Drizzle' => 'rain.png',
		'Hail' => 'hail.png',
		'Hail Showers' => 'hail.png',
		'Freezing Rain' => 'sleet.png',
		'Freezing Drizzle' => 'sleet.png',
		'Freezing Fog' => 'sleet.png',
		'Snow' => 'snow.png',
		'Snow Grains' => 'snow.png',
		'Snow Showers' => 'snow.png',
		'Thunder' => 'thunder_storms.png',
		'Thunderstorm' => 'thunder_storms.png',
		'Thunderstorms and Rain' => 'thunder_storms.png',
		'Thunderstorms and Snow' => 'snow.png',
		'Thunderstorms and Ice Pellets' => 'thunder_storms.png',
		'Thunderstorms with Hail' => 'thunder_storms.png',
		'Thunderstorms with Small Hail' => 'thunder_storms.png',
		'Showers' => 'showers.png',
		'Mist' => 'fog.png',
		'Fog' => 'fog.png',
		'Fog Patches' => 'fog.png',
		'Shallow Fog' => 'fog.png',
		'Partial Fog' => 'fog.png',
		'Windy' => 'windy.png',
	);
	//set weather image
	if (isset($weather_images[$obj['current_observation']['weather']])) {
		$use_img = true;
		$weather = $obj['current_observation']['weather'];
		$weather = str_replace('Heavy ','',$weather);
		$weather = str_replace('Light ','',$weather);
		//set weather image by time of day
		if ($current_time > $sunrise && $current_time < $sunset) {
			$sun_up = true;
		} else {
			$sun_up = false;
		}
		
		$file_path = 'other_graphics/weather/';
		$image_day_exists = file_exists($file_path . 'nt_' . $weather_images[$weather]);
		$image_night_exists = file_exists($file_path . $weather_images[$weather]);
		
		if ($sun_up && $image_day_exists) {
			$use_img = true;
			$img_location = $weather_images[$weather];
		} else if (!$sun_up && ($image_night_exists || $image_day_exists)){
			if ($image_night_exists){
				$use_img = true;
				$img_location = 'nt_' . $weather_images[$weather];
			}else if ($image_day_exists){
				$use_img = true;
				$img_location = $weather_images[$weather];
			}
		} else {
			$use_img = false;
		}
	} else {
		$use_img = false;
	}

	if ($o['logoLeft'] && $logoHeight && $use_img) {
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
				'path' => 'other_graphics/weather/' . $img_location,
				'x' => $boxX,
				'y' => $logoY,
				'w' => $logoHeight,
				'h' => $logoHeight
		);
		$titleXAdjust = $titleWAdjust = $logoXAdjust;
	}

	// Right Logo
	/*
	if ($o['logoRight'] && $logoHeight) {
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
				'h' => $logoHeight,
		);
		$titleWAdjust += $logoXAdjust;
	}
	*/

	$geos[] = array(
				'type' => 'shadowText',
				'font' => 'fontN',
				'gravity' => "left",
				'text' => $weather_top,
				'color' => 'white',
				'x' => $boxX + $titleXAdjust,
				'y' => 1080 - $boxHeight - $bottom + $vbarTextPad,
				'w' => $boxWidth - $titleWAdjust,
				'h' => $o['titleHeight']-$barTextPad*2,
	);

	foreach ($geos as $geo) {
		addGeoToCanvas($canvas, $geo);
	}
}

?>
