<?php

include_once("include.php");
include_once("imagick_include.php");

// TEMPORARY: FIGURE OUT A BETTER LOCATION FOR THIS OR MOVE TO config.php
$animated_headshot_prefix = '/var/www/machac3/rpits/anim_heads/';

$type = $_GET['type'];
$id = $_GET['id'];

if ($type == 'player') {
	$title = getStatscard($id);

	$filename = $title["num"] . $title["first"] . $title["last"];

	$headshotScript = file_get_contents('test_head.js');
	$headshotScript = str_replace("SEQUENCE_REPLACEMENT_STRING", $animated_headshot_prefix . $title["team"] . '/' . $filename . '/' . $filename, $headshotScript);
	$headshotScript = str_replace("BACKGROUND_REPLACEMENT_STRING", $system_path_prefix . 'out/' . $filename . '_noHeadshot.png', $headshotScript);

	echo $headshotScript;


} else {
	http_response_code(400);
	die('Unsupported title type');
}