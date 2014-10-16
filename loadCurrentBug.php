<?php
	include("include.php");
	header("Content-Type: image/png");
	echo(file_get_contents($bug_keyer_url . 'key'));
?>
