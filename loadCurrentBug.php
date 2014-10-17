<?php
	include("include.php");
	header("Content-Type: image/png");
	header("Cache-Control: no-cache, no-store");
	echo(file_get_contents($bug_keyer_url . 'key'));
?>
