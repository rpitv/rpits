<?php

include('include.php');
include('imagick_include.php');

$deets = array('x' => 20, 'y' => 20, 'color' => 'red', 'h' => 60, 'type' => 'slantRectangle');

$start = 50;
$end = 1000;
$step = ($end-$start)/60;

for($i = 0; $i < 60; $i++) {
	$canvas = new Imagick();
	$canvas->newImage(1050, 100, "none", "png");
	$canvas->setImageDepth(8);

	$geo = $deets;
	$geo['w'] = $start + $i*$step;
	$geo['x'] = ($end-$geo['w'])/2;

	addGeoToCanvas($canvas, $geo);

	$file = '/bar' . sprintf('%03d',$i) . '.png';

	$canvas->writeImage(realpath('anim') . $file);

	echo '<img src="anim' . $file . '" />';
	flush();
}

?>
