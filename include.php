<?

include ($includePath . "init.php");
include ($includePath . "getStatscard.php");
include ($includePath . "divingStandings.php");
include ($includePath . "flexBox.php");
include ($includePath . "gameSummary.php");
include ($includePath . "weather.php");

function dbquery($query) {
	$result = mysql_query($query) or die("<b>Error with MySQL Query:</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
	return $result;
}

function dbqueryl($query) {
	$result = mysql_query($query);
	return $result;
}

function queryAssoc($query) {
	$queryResult = dbquery($query);
	$array = array();
	while($row = mysql_fetch_assoc($queryResult)) {
		$array[] = $row;
	}
	return $array;
}

function getTitle($id,$eventId,$withReplacements = true) {
	$titleResult = dbquery("SELECT * from titles where id=\"$id\" LIMIT 1;");
	$title = mysql_fetch_assoc($titleResult);

	// null checking
	if (is_numeric($title["parent"])) {
		$parent = getTitle($title["parent"],$eventId,$withReplacements);
	} else {
		$parent = getTitleFromXML($title["parent"]);
	}

	if ($parent) {

		$title['geos'] = $parent['geos'];
		$title['parentName'] = $parent['name'];

		$cdbResult = dbquery("SELECT * FROM cdb WHERE title_id=\"$id\";");
		while ($row = mysql_fetch_array($cdbResult)) {
			$title['geos'][$row['name']][$row['key']] = $row['value'];
		}

		if ($withReplacements) {
			foreach ($title['geos'] as $key=>$geo) {
				$title['geos'][$key] = tokenReplace($geo,$eventId);
			}
		}
		$title['type'] = 'general';
		return $title;
	}
	return false;
}

function getTitleFromXML($path) {
	if ($path) {
		$file = @fopen($path, "r");
		if ($file) {
			$contents = stream_get_contents($file);
			$xml = new SimpleXMLElement($contents);

			$title['name'] = (string) $xml->name;

			$title['geos'] = getAllChildren($xml->geos);

			return $title;
		}
	}
	return false;
}

function getAllChildren($xml) {
	$children = [];
	$i = 0;
	foreach ($xml->children() as $childXML) {
		$child = getAttributes($childXML);
		if ($child['order']) die("Attribute 'order' is illegal in RML");
		$child['order'] = $i;
		$children[$child['name']] = $child;
		$i++;
	}
	return $children;
}

function getGeoHash($geo) {
	$ignore = ['x','y','name','order'];
	foreach ($ignore as $i) {
		unset($geo[$i]);
	}
	return $geo['type'] . '_' . hash('md4',json_encode($geo));
}

function addGeoToCanvas($canvas,$geo,$bustCacheVar = false) {
	global $bustCache;
	$bustCache = $bustCache || $bustCacheVar;
	$im = getGeoFromCache($geo);
	if (!$im || $bustCache) {
		$im = renderGeo($geo);
		saveGeoToCache($geo,$im);
	}
	$canvas->compositeImage($im, imagick::COMPOSITE_OVER, $geo['x']-10, $geo['y']-10);
}

function renderGeo($geo) {
	$x = 10; $y = 10; $w = 20; $h = 20;
	$canvas = new Imagick();
	$canvas->newImage($geo["w"] + $x + $w, $geo["h"] + $y + $h, "none", 'tga');
	$canvas->setImageDepth(8);
	$canvas->setimagecolorspace(imagick::COLORSPACE_SRGB);
	$geo['x'] = $x;
	$geo['y'] = $y;
	$geo['type']($canvas, $geo);
 
	return $canvas;
}

function getGeoFromCache($geo) {
	$hash = getGeoHash($geo);
	$path = realpath('cache') . "/$hash." . 'tga';
	if (file_exists($path)) {
		$img = new Imagick($path);
		$img->setImageDepth(8);
		$img->setimagecolorspace(imagick::COLORSPACE_SRGB);
		return $img;
	} else {
		return false;
	}
}

function saveGeoToCache($geo,$im) {
	$hash = getGeoHash($geo);
	$im->writeImage(realpath("cache")."/" . $hash . '.' . 'tga') or die ('Error writing Geo to cache');
}

function getTextWidthFromCache($geo) {
	$hash = getGeoHash($geo);
	$result = dbQuery("SELECT * FROM cache WHERE `key` = '$hash' LIMIT 1");
	$hashRow = mysql_fetch_assoc($result);
	if ($hashRow) {
		return $hashRow['hash'];
	} else {
		$width = getTextWidth($geo);
		$result = dbQuery("INSERT INTO `cache` (`key`,`hash`) VALUES ('$hash','$width')");
		return $width;
	}
}

function stripDBFetch($attrs) {
	$result = array();
	foreach ($attrs as $key => $value) {
		if ($key != "x" && $key != "y" && $key != "w" && $key != "h" && $key != "name") {
			$result[$key] = $value;
		}
	}
	return $result;
}

function fetchTeam($team) {
	$teamResource = dbQuery("SELECT * FROM teams WHERE player_abbrev='$team'");
	$teamRow = mysql_fetch_assoc($teamResource);

	$orgResource = dbQuery("SELECT * FROM organizations WHERE code='" . $teamRow['org'] . "'");
	$orgRow = mysql_fetch_assoc($orgResource);

	if ($teamRow && $orgRow) {
		$orgRow['logo'] = 'teamlogos/' . $orgRow['logo'];
		return array_merge($teamRow,$orgRow);
	} else {
		return false;
	}
}

function fetchOrg($org) {
	$orgResource = dbQuery("SELECT * FROM organizations WHERE code='" . $org . "'");
	$orgRow = mysql_fetch_assoc($orgResource);

	$orgRow['logo'] = 'teamlogos/' . $orgRow['logo'];
	return $orgRow;
}

function dbFetch($id, $geo) {
	$result = dbquery("SELECT * FROM cdb WHERE title_id=\"$id\" AND name=\"" . $data["name"] . "\";");
	while ($row = mysql_fetch_array($result)) {
		$geo[$row["key"]] = $row["value"];
	}
	return $geo;
}

function getAttributes($xml) {
	$node = [];
	foreach ($xml->attributes() as $key => $value) {
		$node[$key] = (string) $value;
	}
	if ($node['type']) die("Attribute 'type' is illegal in RML");
	if ($node['value']) die("Attribute 'value' is illegal in RML");
	$node['type'] = $xml->getName();
	if ((string) $xml) {
		$node['value'] = (string) $xml;
	}
	return $node;
}

function tokenReplace($data,$eventId) {
	foreach ($data as $key => $string) {
		$matches = array();

		preg_match_all('/\{(.*?)\}/', $string, $matches);
		$tokens = $matches[1];
		foreach ($tokens as $token) {
			$replacement = getToken($token,$eventId);
			$string = preg_replace('/\{'.$token.'\}/',$replacement,$string);
		}
		$data[$key] = $string;
	}
	return $data;
}

// example: {e.h.color} is the home team's color for the passed in event
function getToken($token,$eventId) {
	$tokens = explode('.',$token);

	// Event based replacement (future expansion on this front is likely)
	if (($tokens[0] == 'e') or ($tokens[0] == 'event')) { // prototype for home/visiting teams
		$eventResource = dbQuery("SELECT * FROM events WHERE id='$eventId'");
		$eventRow = mysql_fetch_assoc($eventResource);
		if ($eventRow) { // pass through to t.team.X.Y.Z
			if ($tokens[1] == "stats") {
				/* data pulled from live stats XML file */
				$statsLink = $eventRow["statsLink"];
				if (array_key_exists(2, $tokens)) {
					$statName = $tokens[2];
					return loadLiveStatsDataCached($statsLink,$eventId)[$statName];
				} else {
					/* if no specific data was requested, just return livestats xml URL */
					return $statsLink;
				}
			} else {
				$teamColumn = $tokens[1] == 'h' ? 'team1' : 'team2';
				$newToken = 't.' . $eventRow[$teamColumn] . '.' . implode('.', array_slice($tokens, 2));
				return getToken($newToken, $eventId);
			}
		}
	} else if (($tokens[0] == 'o') or ($tokens[0] == 'org')) { // return org element
		$org = fetchOrg($tokens[1]);
		if ($org) {
			return $org[$tokens[2]];
		}
	} else if (($tokens[0] == 't') or ($tokens[0] == 'team')) {
		$team = fetchTeam($tokens[1]);
		if ($team and (($tokens[2] == 'p') or ($tokens[2] == 'player'))) {
			$playerData = dbQuery("SELECT * FROM players WHERE team='" . $tokens[1] ."' AND " . $tokens[3] . "='" . $tokens[4] . "'");
			$player = mysql_fetch_array($playerData);
			if ($tokens[5] == 'name') { // case for full name
				return $player['first'] . ' ' . $player['last'];
			} else if (($tokens[5] == 'record') and ($player['stype'] == 'hg')) { // case for goalie record
				return $player['s2'] . '-' . $player['s3'] . '-' . $player['s4'];
			} else {
				return $player[$tokens[5]];
			}
		} else if ($team) { // return team element
			return $team[$tokens[2]];
		}
	}
	return false;
}

function rgbhex($red, $green, $blue) {
	$red = 0x10000 * max(0, min(255, $red + 0));
	$green = 0x100 * max(0, min(255, $green + 0));
	$blue = max(0, min(255, $blue + 0));
	return "#" . str_pad(strtoupper(dechex($red + $green + $blue)), 6, "0", STR_PAD_LEFT);
}

function mysqlQueryToJsonArray($query) {
	$result = dbquery($query);
	$columns = array();
	$rows = array();
	while ($row = mysql_fetch_assoc($result)) {
		$columns = array();
		$dataRow = array();
		foreach ($row as $key => $value) {
			$columns[] = $key;
			$dataRow[] = $value;
		}
		$rows[] = $dataRow;
	}
	$data['rows'] = $rows;
	$data['columns'] = $columns;
	return json_encode($data);
}

function timestamp ($str) {
	global $metrics, $startTime, $lastTime;
	$now = microtime(true);
	if ($metrics) {
		printf("%.5f",($now - $startTime));
		echo ' - ';
		printf("%.5f",($now - $lastTime));
		echo ' - '. $str . '<br>';
	}
	$lastTime = $now;
}

function groupGeosByType($geos) {
	$return = Array();
	foreach ($geos as $geo) {
		if (!$return[$geo['type']]) {
			$return[$geo['type']] = Array();
		}
		$return[$geo['type']][] = $geo;
	}
	return $return;
}

function checkHashForTitle($title,$key = false) {
	if (!$key) {
		$key = $title['id'];
	}
	$geoHash = hash('md4',json_encode($title['geos']));
	$result = dbquery("SELECT * FROM cache WHERE `key`='" . $key . "' LIMIT 1");
	$cacheRow = mysql_fetch_assoc($result);

	if ($geoHash == $cacheRow["hash"]) {
		return true;
	} else {
		return false;
	}
}

function getHashForTitle($title) {
	return $geoHash = hash('md4',json_encode($title['geos']));
}

function getAnimationScriptForTitle($title) {

	$animated_headshot_prefix = realpath('anim_heads') . '/';
	if ($title['type'] == 'player') {
		$filename = $title["num"] . $title["first"] . $title["last"];

		$headshotScript = file_get_contents('test_head.js');
		$headshotScript = str_replace("SEQUENCE_REPLACEMENT_STRING", $animated_headshot_prefix . $title["team"] . '/' . $filename . '/' . $filename, $headshotScript);
		$headshotScript = str_replace("BACKGROUND_REPLACEMENT_STRING", realpath('out/' . $filename . '_noHeadshot.png'), $headshotScript);

		foreach($title['geos'] as $geo) {
			if($geo['name'] == 'headshot') {
				$headshotScript = str_replace("HEADSHOT_REPLACEMENT_STRING", realpath('cache/' . getGeoHash($geo) . '.tga'), $headshotScript);
			}
		}

		return $headshotScript;

	} else {
		return false;
	}
}

/*
 * Given a URL, return a SimpleXMLElement representing the contents
 * of that URL. The SimpleXMLElement object returned by this function 
 * may be cached and reused for future requests, so it should not be modified.
 */
function loadXmlCached($source) {
	static $cache = array();

	if (!array_key_exists($source, $cache)) {
		$file = fopen($source, "r");
		$contents = stream_get_contents($file);	
		$cache[$source] = new SimpleXMLElement($contents);
	}

	return $cache[$source];
}

/*
 * Load some useful things from live stats, for use in token replacements.
 * (For now, just officials.)
 */
function loadLiveStatsDataCached($source,$eventId) {
	static $cache = false;

	if ($cache == false) {
		/* Each element X in this array corresponds to token e.stats.X */
		$cache = array();

		$livestats = loadXmlCached($source);

		/* Pull officials */
		$referees = $livestats->xpath("//official[@title='Referee']");
		$linesmen = $livestats->xpath("//official[@title='Linesman']");
		$cache["referee1"] = $referees[0]["name"];
		$cache["referee2"] = $referees[1]["name"];
		$cache["linesman1"] = $linesmen[0]["name"];
		$cache["linesman2"] = $linesmen[1]["name"];
		
		/* Pull Stats */
		$hgame = $livestats->xpath("//team[@vh = 'H']/linescore");
		$vgame = $livestats->xpath("//team[@vh = 'V']/linescore");
		$hperiods = $livestats->xpath("//team[@vh = 'H']/linescore/lineprd");
		$vperiods = $livestats->xpath("//team[@vh = 'V']/linescore/lineprd");
		if($hgame[0]["periods"] > 0)
		{
			$hscore = 0;
			$hshots = 0;
			$hsaves = 0;
			$hpen = 0;
			$hpen_min = 0;
			$hfowon = 0;
			for($x = 0; $x <= $hgame[0]["periods"] ; $x++)
			{
				$hscore += (int)$hperiods[$x]["score"];
				$hshots += (int)$hperiods[$x]["shots"];
				$hsaves += (int)$hperiods[$x]["saves"];
				$hpen += (int)$hperiods[$x]["pen"];
				$hpen_min += (int)$hperiods[$x]["pmin"];
				$hfowon += (int)$hperiods[$x]["fowon"];
			}
			$cache["hscore"] = $hscore;
			$cache["hshots"] = $hshots;
			$cache["hsaves"] = $hsaves;
			$cache["hpen"] = $hpen;
			$cache["hpen_min"] = $hpen_min;
			$cache["hfowon"] = $hfowon;
		}
		if($vgame[0]["periods"] > 0)
		{
			$vscore = 0;
			$vshots = 0;
			$vsaves = 0;
			$vpen = 0;
			$vpen_min = 0;
			$vfowon = 0;
			for($x = 0; $x <= $vgame[0]["periods"] ; $x++)
			{
				$vscore += (int)$vperiods[$x]["score"];
				$vshots += (int)$vperiods[$x]["shots"];
				$vsaves += (int)$vperiods[$x]["saves"];
				$vpen += (int)$vperiods[$x]["pen"];
				$vpen_min += (int)$vperiods[$x]["pmin"];
				$vfowon += (int)$vperiods[$x]["fowon"];
			}
			$cache["vscore"] = $vscore;
			$cache["vshots"] = $vshots;
			$cache["vsaves"] = $vsaves;
			$cache["vpen"] = $vpen;
			$cache["vpen_min"] = $vpen_min;
			$cache["vfowon"] = $vfowon;
		}
		
		/* Pull Players */
		$hteam = $livestats->xpath("//team[@vh = 'H']/lines/line");
		$vteam = $livestats->xpath("//team[@vh = 'V']/lines/line");
		$hfullteam = $livestats->xpath("//team[@vh = 'H']/player");
		$vfullteam = $livestats->xpath("//team[@vh = 'V']/player");
		
		$hplayers = array();
		$vplayers = array();
		
		for($x = 0; $x < count($hfullteam) ; $x++)
		{
			$name_split = explode(" ", $hfullteam[$x]["name"]);
			$part_1 = $name_split[1];
			$part_2 = $name_split[0];
			$hplayers[(int)$hfullteam[$x]["code"]] = $part_2 . " " . $part_1; 
		}
		for($x = 0; $x < count($vfullteam) ; $x++)
		{
			if (strpos($vfullteam[$x]["name"],',') !== false)
			{
				$name_split = explode(",", $vfullteam[$x]["name"]);
				$part_1 = (string)ucfirst(strtolower((string)$name_split[0]));
				$part_2 = (string)ucfirst(strtolower((string)$name_split[1]));
				$vplayers[(int)$vfullteam[$x]["code"]] = $name_split[1] . " " . $name_split[0];
			}
			else
			{
				$name_split = explode(" ", $vfullteam[$x]["name"]);
				$part_1 = $name_split[1];
				$part_2 = $name_split[0];
				$vplayers[(int)$vfullteam[$x]["code"]] = $part_2 . " " . $part_1; 
			}			
		}
		/*Home*/
		/* First Line */
		$cache["hlw1"] = $hteam[0]["lw"] . " - " . $hplayers[(int)$hteam[0]["lw"]];
		$cache["hlw1_pic"] = str_replace(' ', '', $hplayers[(int)$hteam[0]["lw"]]);
		$cache["hc1"] = $hteam[0]["c"] . " - " . $hplayers[(int)$hteam[0]["c"]];
		$cache["hc1_pic"] = str_replace(' ', '', $hplayers[(int)$hteam[0]["c"]]);
		$cache["hrw1"] = $hteam[0]["rw"] . " - " . $hplayers[(int)$hteam[0]["rw"]];
		$cache["hrw1_pic"] = str_replace(' ', '', $hplayers[(int)$hteam[0]["rw"]]);
		$cache["hld1"] = $hteam[0]["ld"] . " - " . $hplayers[(int)$hteam[0]["ld"]];
		$cache["hld1_pic"] = str_replace(' ', '', $hplayers[(int)$hteam[0]["ld"]]);
		$cache["hrd1"] = $hteam[0]["rd"] . " - " . $hplayers[(int)$hteam[0]["rd"]];
		$cache["hrd1_pic"] = str_replace(' ', '', $hplayers[(int)$hteam[0]["rd"]]);
		$cache["hg1"] = $hteam[0]["g"] . " - " . $hplayers[(int)$hteam[0]["g"]];
		$cache["hg1_pic"] = str_replace(' ', '', $hplayers[(int)$hteam[0]["g"]]);
		/* Second Line */
		$cache["hlw2"] = $hteam[1]["lw"] . " - " . $hplayers[(int)$hteam[1]["lw"]];
		$cache["hlw2_pic"] = str_replace(' ', '', $hplayers[(int)$hteam[1]["lw"]]);
		$cache["hc2"] = $hteam[1]["c"] . " - " . $hplayers[(int)$hteam[1]["c"]];
		$cache["hc2_pic"] = str_replace(' ', '', $hplayers[(int)$hteam[1]["c"]]);
		$cache["hrw2"] = $hteam[1]["rw"] . " - " . $hplayers[(int)$hteam[1]["rw"]];
		$cache["hrw2_pic"] = str_replace(' ', '', $hplayers[(int)$hteam[1]["rw"]]);
		$cache["hld2"] = $hteam[1]["ld"] . " - " . $hplayers[(int)$hteam[1]["ld"]];
		$cache["hld2_pic"] = str_replace(' ', '', $hplayers[(int)$hteam[1]["ld"]]);
		$cache["hrd2"] = $hteam[1]["rd"] . " - " . $hplayers[(int)$hteam[1]["rd"]];
		$cache["hrd2_pic"] = str_replace(' ', '', $hplayers[(int)$hteam[1]["rd"]]);
		$cache["hg2"] = $hteam[1]["g"] . " - " . $hplayers[(int)$hteam[1]["g"]];
		$cache["hg2_pic"] = str_replace(' ', '', $hplayers[(int)$hteam[1]["g"]]);
		/* Third Line */
		$cache["hlw3"] = $hteam[2]["lw"] . " - " . $hplayers[(int)$hteam[2]["lw"]];
		$cache["hlw3_pic"] = str_replace(' ', '', $hplayers[(int)$hteam[2]["lw"]]);
		$cache["hc3"] = $hteam[2]["c"] . " - " . $hplayers[(int)$hteam[2]["c"]];
		$cache["hc3_pic"] = str_replace(' ', '', $hplayers[(int)$hteam[2]["c"]]);
		$cache["hrw3"] = $hteam[2]["rw"] . " - " . $hplayers[(int)$hteam[2]["rw"]];
		$cache["hrw3_pic"] = str_replace(' ', '', $hplayers[(int)$hteam[2]["rw"]]);
		$cache["hld3"] = $hteam[2]["ld"] . " - " . $hplayers[(int)$hteam[2]["ld"]];
		$cache["hld3_pic"] = str_replace(' ', '', $hplayers[(int)$hteam[2]["ld"]]);
		$cache["hrd3"] = $hteam[2]["rd"] . " - " . $hplayers[(int)$hteam[2]["rd"]];
		$cache["hrd3_pic"] = str_replace(' ', '', $hplayers[(int)$hteam[2]["rd"]]);
		$cache["hg3"] = $hteam[2]["g"] . " - " . $hplayers[(int)$hteam[2]["g"]];
		$cache["hg3_pic"] = str_replace(' ', '', $hplayers[(int)$hteam[2]["g"]]);
		/* Fourth Line */
		$cache["hlw4"] = $hteam[3]["lw"] . " - " . $hplayers[(int)$hteam[3]["lw"]];
		$cache["hlw4_pic"] = str_replace(' ', '', $hplayers[(int)$hteam[3]["lw"]]);
		$cache["hc4"] = $hteam[3]["c"] . " - " . $hplayers[(int)$hteam[3]["c"]];
		$cache["hc4_pic"] = str_replace(' ', '', $hplayers[(int)$hteam[3]["c"]]);
		$cache["hrw4"] = $hteam[3]["rw"] . " - " . $hplayers[(int)$hteam[3]["rw"]];
		$cache["hrw4_pic"] = str_replace(' ', '', $hplayers[(int)$hteam[3]["rw"]]);
		$cache["hld4"] = $hteam[3]["ld"] . " - " . $hplayers[(int)$hteam[3]["ld"]];
		$cache["hld4_pic"] = str_replace(' ', '', $hplayers[(int)$hteam[3]["ld"]]);
		$cache["hrd4"] = $hteam[3]["rd"] . " - " . $hplayers[(int)$hteam[3]["rd"]];	
		$cache["hrd4_pic"] = str_replace(' ', '', $hplayers[(int)$hteam[3]["rd"]]);
		$cache["hg4"] = $hteam[3]["g"] . " - " . $hplayers[(int)$hteam[3]["g"]];
		$cache["hg4_pic"] =str_replace(' ', '', $hplayers[(int)$hteam[3]["g"]]);
		
		$cache["hg1_stats"] = getToken("e.h.p.num.".$hteam[0]["g"].".record",$eventId)."\n".getToken("e.h.p.num.".$hteam[0]["g"].".s6",$eventId)."\n".getToken("e.h.p.num.".$hteam[0]["g"].".s5",$eventId);
		
		/*Away*/
		/* First Line */
		$cache["vlw1"] = $vteam[0]["lw"] . " - " . $vplayers[(int)$vteam[0]["lw"]];
		$cache["vlw1_pic"] = str_replace(' ', '', $vplayers[(int)$vteam[0]["lw"]]);
		$cache["vc1"] = $vteam[0]["c"] . " - " . $vplayers[(int)$vteam[0]["c"]];
		$cache["vc1_pic"] = str_replace(' ', '', $vplayers[(int)$vteam[0]["c"]]);
		$cache["vrw1"] = $vteam[0]["rw"] . " - " . $vplayers[(int)$vteam[0]["rw"]];
		$cache["vrw1_pic"] = str_replace(' ', '', $vplayers[(int)$vteam[0]["rw"]]);
		$cache["vld1"] = $vteam[0]["ld"] . " - " . $vplayers[(int)$vteam[0]["ld"]];
		$cache["vld1_pic"] = str_replace(' ', '', $vplayers[(int)$vteam[0]["ld"]]);
		$cache["vrd1"] = $vteam[0]["rd"] . " - " . $vplayers[(int)$vteam[0]["rd"]];
		$cache["vrd1_pic"] = str_replace(' ', '', $vplayers[(int)$vteam[0]["rd"]]);
		$cache["vg1"] = $vteam[0]["g"] . " - " . $vplayers[(int)$vteam[0]["g"]];
		$cache["vg1_pic"] = str_replace(' ', '', $vplayers[(int)$vteam[0]["g"]]);
		/* Second Line */
		$cache["vlw2"] = $vteam[1]["lw"] . " - " . $vplayers[(int)$vteam[1]["lw"]];
		$cache["vlw2_pic"] = str_replace(' ', '', $vplayers[(int)$vteam[1]["lw"]]);
		$cache["vc2"] = $vteam[1]["c"] . " - " . $vplayers[(int)$vteam[1]["c"]];
		$cache["vc2_pic"] = str_replace(' ', '', $vplayers[(int)$vteam[1]["c"]]);
		$cache["vrw2"] = $vteam[1]["rw"] . " - " . $vplayers[(int)$vteam[1]["rw"]];
		$cache["vrw2_pic"] = str_replace(' ', '', $vplayers[(int)$vteam[1]["rw"]]);
		$cache["vld2"] = $vteam[1]["ld"] . " - " . $vplayers[(int)$vteam[1]["ld"]];
		$cache["vld2_pic"] = str_replace(' ', '', $vplayers[(int)$vteam[1]["ld"]]);
		$cache["vrd2"] = $vteam[1]["rd"] . " - " . $vplayers[(int)$vteam[1]["rd"]];
		$cache["vrd2_pic"] = str_replace(' ', '', $vplayers[(int)$vteam[1]["rd"]]);
		$cache["vg2"] = $vteam[1]["g"] . " - " . $vplayers[(int)$vteam[1]["g"]];
		$cache["vg2_pic"] = str_replace(' ', '', $vplayers[(int)$vteam[1]["g"]]);
		/* Third Line */
		$cache["vlw3"] = $vteam[2]["lw"] . " - " . $vplayers[(int)$vteam[2]["lw"]];
		$cache["vlw3_pic"] = str_replace(' ', '', $vplayers[(int)$vteam[2]["lw"]]);
		$cache["vc3"] = $vteam[2]["c"] . " - " . $vplayers[(int)$vteam[2]["c"]];
		$cache["vc3_pic"] = str_replace(' ', '', $vplayers[(int)$vteam[2]["c"]]);
		$cache["vrw3"] = $vteam[2]["rw"] . " - " . $vplayers[(int)$vteam[2]["rw"]];
		$cache["vrw3_pic"] = str_replace(' ', '', $vplayers[(int)$vteam[2]["rw"]]);
		$cache["vld3"] = $vteam[2]["ld"] . " - " . $vplayers[(int)$vteam[2]["ld"]];
		$cache["vld3_pic"] = str_replace(' ', '', $vplayers[(int)$vteam[2]["ld"]]);
		$cache["vrd3"] = $vteam[2]["rd"] . " - " . $vplayers[(int)$vteam[2]["rd"]];
		$cache["vrd3_pic"] = str_replace(' ', '', $vplayers[(int)$vteam[2]["rd"]]);
		$cache["vg3"] = $vteam[2]["g"] . " - " . $vplayers[(int)$vteam[2]["g"]];
		$cache["vg3_pic"] = str_replace(' ', '', $vplayers[(int)$vteam[2]["g"]]);
		/* Fourth Line */
		$cache["vlw4"] = $vteam[3]["lw"] . " - " . $vplayers[(int)$vteam[3]["lw"]];
		$cache["vlw4_pic"] = str_replace(' ', '', $vplayers[(int)$vteam[3]["lw"]]);
		$cache["vc4"] = $vteam[3]["c"] . " - " . $vplayers[(int)$vteam[3]["c"]];
		$cache["vc4_pic"] = str_replace(' ', '', $vplayers[(int)$vteam[3]["c"]]);
		$cache["vrw4"] = $vteam[3]["rw"] . " - " . $vplayers[(int)$vteam[3]["rw"]];
		$cache["vrw4_pic"] = str_replace(' ', '', $vplayers[(int)$vteam[3]["rw"]]);
		$cache["vld4"] = $vteam[3]["ld"] . " - " . $vplayers[(int)$vteam[3]["ld"]];
		$cache["vld4_pic"] = str_replace(' ', '', $vplayers[(int)$vteam[3]["ld"]]);
		$cache["vrd4"] = $vteam[3]["rd"] . " - " . $vplayers[(int)$vteam[3]["rd"]];	
		$cache["vrd4_pic"] = str_replace(' ', '', $vplayers[(int)$vteam[3]["rd"]]);
		$cache["vg4"] = $vteam[3]["g"] . " - " . $vplayers[(int)$vteam[3]["g"]];
		$cache["vg4_pic"] =str_replace(' ', '', $vplayers[(int)$vteam[3]["g"]]);
		
		$cacve["vg1_stats"] = getToken("e.v.p.num.".$vteam[0]["g"].".record",$eventId)."\n".getToken("e.v.p.num.".$vteam[0]["g"].".s6",$eventId)."\n".getToken("e.v.p.num.".$vteam[0]["g"].".s5",$eventId);
		
		/* Pull Random Stats */
		$venue_stats = $livestats->xpath("//venue");
		
		$cache["attendance"] = $venue_stats[0]["attend"]; 
		$cache["hid"] = $venue_stats[0]["homeid"];
		$cache["vid"] = $venue_stats[0]["visid"];
		$cache["date"] = $venue_stats[0]["date"];
		
		$hrec = $livestats->xpath("//team[@vh = 'H']")[0]["record"];
		$vrec = $livestats->xpath("//team[@vh = 'V']")[0]["record"];
		
		if (strpos($hrec,';') !== false)
		{
			$cache["hrecord"] = str_replace(";"," (",$livestats->xpath("//team[@vh = 'H']")[0]["record"]).")" ;
		}
		else 
		{
			$cache["hrecord"] = $hrec;
		}	
		if(strpos($vrec,';') !== false)
		{
			$cache["vrecord"] = str_replace(";"," (",$livestats->xpath("//team[@vh = 'V']")[0]["record"]).")" ;
		}
		else
		{
			$cache["vrecord"] =  $vrec;
		}
		
		
		/* Pull Penalties */
		$penalties = $livestats->xpath("//pen");
		$h = false;
		$v = false;
		for($x = count($penalties)-1; $x >= 0 ; $x--)
		{
			if($penalties[$x]["vh"] == "H" && !$h)
			{
				$h = true;
				if (strpos($penalties[$x]["name"],',') !== false)
				{
					$name_split = explode(",", $penalties[$x]["name"]);
					$part_1 = (string)ucfirst(strtolower((string)$name_split[0]));
					$part_2 = (string)ucfirst(strtolower((string)$name_split[1]));
					$cache["hcpen"] = $name_split[1] . " " . $name_split[0];
					$cache["hcpentype"] = ucfirst(strtolower($penalties[$x]["desc"]));
				}
				else
				{
					$name_split = explode(" ", $penalties[$x]["name"]);
					$part_1 = $name_split[1];
					$part_2 = $name_split[0];
					$cache["hcpen"] = $part_2 . " " . $part_1;					
					$cache["hcpentype"] = ucfirst(strtolower($penalties[$x]["desc"]));
				}	
			}
			if($penalties[$x]["vh"] == "V" && !$v)
			{
				$v = true;
				if (strpos($penalties[$x]["name"],',') !== false)
				{
					$name_split = explode(",", $penalties[$x]["name"]);
					$part_1 = (string)ucfirst(strtolower((string)$name_split[0]));
					$part_2 = (string)ucfirst(strtolower((string)$name_split[1]));
					$cache["vcpen"] = $name_split[1] . " " . $name_split[0];
					$cache["vcpentype"] = ucfirst(strtolower($penalties[$x]["desc"]));
				}
				else
				{
					$name_split = explode(" ", $penalties[$x]["name"]);
					$part_1 = $name_split[1];
					$part_2 = $name_split[0];
					$cache["vcpen"] = $part_2 . " " . $part_1; 
					$cache["vcpentype"] = ucfirst(strtolower($penalties[$x]["desc"]));
				}	
			}
		}
		/* FOOTBALL */
		
		/*Pull Stats */
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
	}
	return $cache;
}

?>
