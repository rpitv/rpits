<?php

include ("include.php");
include ("chn_scraper.php");

$teamId = $_GET["tid"];

$page = fopen("http://www.collegehockeynews.com/stats/team/xxxx/$teamId","r");

$contents = stream_get_contents($page);

echo($contents);

$values = parser($contents);

echo "<table>";

foreach ($values as $players) {
	echo update($players);
}
echo "</table>";

?>
