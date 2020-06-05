<?php

$includePath = $includePath ?? './';

if(!file_exists($includePath . 'config.php'))
{
	die("<h1>Please rename config.sample.php to config.php and edit MYSQL server information appropriately.</h1>");
}

include($includePath . 'config.php');

if(!defined('IMGFMT')) {
	define('IMGFMT','png');
}

$mysqli = new mysqli($mysql_server_address,$mysql_server_username,$mysql_server_password,$mysql_database_name);

if ($mysqli->connect_errno) {
	die("<h1>Coud not connect to MYSQL server. Please ensure server details in config.php are correct.</h1><p>(" . $mysqli->connect_errno . ") " . $mysqli->connect_error . "</p>");
}

// mysql_select_db($mysql_database_name) or die ("<h1>Could not select database " . $mysql_database_name . ". Please ensure that a database is defined in config.php and that the database exists.");

$mysqli->query('SET NAMES "utf8" COLLATE "utf8_general_ci";');

$startTime = microtime(true);
$lastTime = $startTime;

$metrics = $_GET["metrics"] ?? '';
if($metrics) echo ('<pre>');

?>
