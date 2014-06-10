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


function flexBox(&$canvas,$o,$bustCache = false) {

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
	
	$geos[] = array(
			'type' => 'plainText',
			'font' => 'fontN',
			'gravity' => 'west',
			'text' => $o['bodyText'],
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
	if ($subTitleWidth < 0) {
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

	//
	// Left Logo
	//

	if ($o['logoLeft'] && $logoHeight) {
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
				'path' => $o['logoLeft'],
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
				'h' => $logoHeight
		);
		$titleWAdjust += $logoXAdjust;
	}





	$geos[] = array(
				'type' => 'shadowText',
				'font' => 'fontN',
				'gravity' => $o['titleGravity'],
				'text' => $o['titleText'],
				'color' => 'white',
				'x' => $boxX + $titleXAdjust,
				'y' => 1080 - $boxHeight - $bottom + $barTextPad,
				'w' => $boxWidth - $titleWAdjust,
				'h' => $o['titleHeight']-$barTextPad*2
		);

	//echo '<pre>'; print_r($geos);

	foreach ($geos as $geo) {
		addGeoToCanvas($canvas, $geo);
	}

	//return $localCanvas;
}

?>
