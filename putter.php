<?php

include("config.php");
include("include.php");

$time_start = microtime();
$pre_get;
$post_get;
$pre_post;
$post_post;
$log = sprintf("<b>%.3f</b> - ", fmod(microtime(true), 60));

function do_request($method,$url, $data, &$log, $optional_headers = null) {
	$params = array('http' => array(
					'method' => $method,
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

$path = $_GET["path"];
$command = $_GET["command"];
$server = $_GET["server"];

$method = "POST";
$data = "";
if($server == 'animator') {
	$method = "PUT";
	if($_GET["type"] && $_GET["type"] == 'player') {
		$title = getStatscard($_GET["id"]);
		$data = getAnimationScriptForTitle($title);
		$command = 'script';
		$log .= "Animator Script: " . $_GET['type'] . ' ' . $_GET['id'];
	} else if ($_GET["type"]) {
		http_response_code(400);
		die('Title type not supported for animation');
	} else {
		$data = $command;
		$command = 'command'; // does this make sense? OH WELL
		$log .= "Animator Command: $data, ";
	}
	
} else if (strlen($command) > 0) {

	$log .= "Command: $command, ";
	$headers = "Content-Length: 0\n";
} else {
	$command = "key";
	$pre_get = microtime();
	if (strlen($path) > 1) {
		$data = file_get_contents("$path");
		$log .= "PNG Path: $path, ";
	}
	if (!$data) {
		$data = file_get_contents($system_path_prefix . "assets/blank.png");
		$log .= "ERROR: Couldn't load title; clearing existing title";
	}

	$post_get = microtime();
}

$server_url = $server == 'animator' ? $animator_url : $keyer_url;

$pre_post = microtime();
$result = do_request($method,$server_url . "$command", $data, $log, $headers);
$post_post = microtime();

// re-write timing at some point
$time_end = microtime();
$post_time = sprintf("%.3f", $post_post - $pre_post);
$get_time = sprintf("%.3f", $post_get - $pre_get);
$total_time = sprintf("%.3f", $time_end - $time_start);
$log .= "Total: $total_time,  Get: $get_time , Post: $post_time. <br>";
echo $log;
//echo("<hr>");
?>