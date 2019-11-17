<?php

function fb_stats($source,$eventId) {
	
	$livestats = loadXmlCached($source);
	$cache = array();
	$cache["fbvscore"] = $livestats->xpath("//team[@vh = 'V']/linescore")[0]["score"];
	$cache["fbhscore"] = $livestats->xpath("//team[@vh = 'H']/linescore")[0]["score"];
	$cache["fbhpenyds"] = $livestats->xpath("//team[@vh = 'H']/totals/penalties")[0]["yds"];
	$cache["fbvpenyds"] = $livestats->xpath("//team[@vh = 'V']/totals/penalties")[0]["yds"];
	$cache["fbhsacks"] = $livestats->xpath("//team[@vh = 'H']/totals/defense")[0]["sacks"];
	$cache["fbvsacks"] = $livestats->xpath("//team[@vh = 'V']/totals/defense")[0]["sacks"];
	$cache["fbhpassyds"] = $livestats->xpath("//team[@vh = 'H']/totals/pass")[0]["yds"];
	$cache["fbvpassyds"] = $livestats->xpath("//team[@vh = 'V']/totals/pass")[0]["yds"];
	$cache["fbhrushyds"] = $livestats->xpath("//team[@vh = 'H']/totals/rush")[0]["yds"];
	$cache["fbvrushyds"] = $livestats->xpath("//team[@vh = 'V']/totals/rush")[0]["yds"];
	$cache["fbhturn"] = (int)$livestats->xpath("//team[@vh = 'H']/totals/fumbles")[0]["lost"];
	$cache["fbvturn"] = (int)$livestats->xpath("//team[@vh = 'V']/totals/fumbles")[0]["lost"];
	$cache["fbhturn"] += (int)$livestats->xpath("//team[@vh = 'H']/totals/pass")[0]["int"];
	$cache["fbvturn"] += (int)$livestats->xpath("//team[@vh = 'V']/totals/pass")[0]["int"];
	return $cache;
}
?>