<?php

if(!file_exists($includePath . 'config.php'))
{
	die("<h1>Please rename config.sample.php to config.php and edit database information appropriately.</h1>");
}

include($includePath . 'config.php');

if(!defined('IMGFMT')) {
	define('IMGFMT','png');
}

$startTime = microtime(true);
$lastTime = $startTime;

$metrics = $_GET["metrics"];
if($metrics) echo ('<pre>');

?>
