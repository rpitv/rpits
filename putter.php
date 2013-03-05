<?php

include("config.php");

$time_start = microtime();
$pre_get;
$post_get;
$pre_post;
$post_post;
$log = sprintf("<b>%.3f</b> - ", fmod(microtime(true), 60));

function do_post_request($url, $data, &$log, $optional_headers = null) {
	$params = array('http' => array(
					'method' => 'POST',
					'content' => $data
					));
	if ($optional_headers !== null) {
		$params['http']['header'] = $optional_headers;
	}
	$ctx = stream_context_create($params);
	$fp = @fopen($url, 'rb', false, $ctx);
	if ($http_response_header[0] == "HTTP/1.1 503 Service Unavailable") {
		$log .= "<b style=\"color:red\">503</b>, ";
	}
	if (!$fp) {
		//throw new Exception("Problem with $url, $php_errormsg <br>");
	}
	$response = @stream_get_contents($fp);
	if ($response === false) {
		//throw new Exception("Problem reading data from $url, $php_errormsg <br>");
	}
	return $response;
}

$id = $_GET["id"];
$type = $_GET["type"];
$path = $_GET["path"];
$command = $_GET["command"];

if (strlen($command) > 0) {

	$log .= "Command: $command, ";
	$headers = "Content-Length: 0\n";
	$svg = "";
} else {
	$command = "key";
	$pre_get = microtime();
	if ($type == "player") {
		$svg = file_get_contents($system_path_prefix . "svg_gen.php?id=$id&type=$type");
		$log .= "Player ID: $id, ";
	} else if ($type == "billboard") {
		$svg = file_get_contents($system_path_prefix . "svg_gen.php?id=$id&type=$type");
		$log .= "Billboard ID: $id, ";
	} else if (strlen($path) > 1) {
		$svg = file_get_contents("$path");
		$log .= "PNG Path: $path, ";
	} else if ($type == "svg") {
		$svg = file_get_contents($system_path_prefix . "svg_card.php?id=$id");
		$log .= "SVG card test ID: $id, ";
	} else {
		$svg = file_get_contents($system_path_prefix . "svg_gen.php?id=$id&type=$type");
		$log .= "General ID: $id, ";
	}
	if (!$svg) {
		$svg = file_get_contents($system_path_prefix . "assets/blank.png");
		$log .= "ERROR: Couldn't load title; clearing existing title";
	}

	$post_get = microtime();

	//$headers = "Content-Type: image/svg+xml\n";
	//echo $svg;
}

$pre_post = microtime();
$result = do_post_request($keyer_url . "/$command", $svg, $log, $headers);
$post_post = microtime();

//echo($result);
///echo("<br>\n");
$time_end = microtime();
$post_time = sprintf("%.3f", $post_post - $pre_post);
$get_time = sprintf("%.3f", $post_get - $pre_get);
$total_time = sprintf("%.3f", $time_end - $time_start);
$log .= "Total: $total_time,  Get: $get_time , Post: $post_time. <br>";
echo $log;
//echo("<hr>");
?>