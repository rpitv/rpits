<?

if (!isset($includePath)) {
    $includePath = "./";
}

include ($includePath . "init.php");
include ($includePath . "getStatscard.php");
include ($includePath . "divingStandings.php");
include ($includePath . "wrpi_standings.php"); 
include ($includePath . "flexBox.php");
include ($includePath . "gameSummary.php");
include ($includePath . "weather.php");
include ($includePath . "fb_stats.php");
include ($includePath . "hockey_stats.php");

function dbquery($query) {
	$result = rpits_db_query($query) or die("<b>Error with Database Query:</b>.\n<br />Query: " . $query . "<br />\nError: (" . rpits_db_errno() . ") " . rpits_db_error());
	return $result;
}

function dbqueryl($query) {
	$result = rpits_db_query($query);
	return $result;
}

function queryAssoc($query) {
	$queryResult = dbquery($query);
	$array = array();
	while($row = rpits_db_fetch_assoc($queryResult)) {
		$array[] = $row;
	}
	return $array;
}

function getTitle($id,$eventId,$withReplacements = true) {
	$titleResult = dbquery("SELECT * from titles where id=\"$id\" LIMIT 1;");
	$title = rpits_db_fetch_assoc($titleResult);

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
		while ($row = rpits_db_fetch_array($cdbResult)) {
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
	echo "Work Dammit";
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
	$hashRow = rpits_db_fetch_assoc($result);
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
	$teamRow = rpits_db_fetch_assoc($teamResource);

	$orgResource = dbQuery("SELECT * FROM organizations WHERE code='" . $teamRow['org'] . "'");
	$orgRow = rpits_db_fetch_assoc($orgResource);

	if ($teamRow && $orgRow) {
		$orgRow['logo'] = 'teamlogos/' . $orgRow['logo'];
		return array_merge($teamRow,$orgRow);
	} else {
		return false;
	}
}

function fetchOrg($org) {
	$orgResource = dbQuery("SELECT * FROM organizations WHERE code='" . $org . "'");
	$orgRow = rpits_db_fetch_assoc($orgResource);

	$orgRow['logo'] = 'teamlogos/' . $orgRow['logo'];
	return $orgRow;
}

function dbFetch($id, $geo) {
	$result = dbquery("SELECT * FROM cdb WHERE title_id=\"$id\" AND name=\"" . $data["name"] . "\";");
	while ($row = rpits_db_fetch_array($result)) {
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
		$eventRow = rpits_db_fetch_assoc($eventResource);
		if ($eventRow) { // pass through to t.team.X.Y.Z
			if ($tokens[1] == "stats") {
				/* data pulled from live stats XML file */
				$statsLink = $eventRow["statsLink"];
				if (array_key_exists(2, $tokens)) {
					if($tokens[2] == "season") {
						if(array_key_exists(3, $tokens)) {
							if($tokens[3] == "wh") {
								if(array_key_exists(4, $tokens)) {
								return loadWRPISeasonStandings_wh($tokens[4],$eventId);
								}
							}
							if($tokens[3] == "mh") {
								if(array_key_exists(4, $tokens)) {
								return loadWRPISeasonStandings_mh($tokens[4],$eventId);
								}
							}
						}
					}
					else if($tokens[2] == "fb") {
						if(array_key_exists(3, $tokens)) {
							return fb_stats($statsLink,$eventId)[$tokens[3]];
						}
					}
					else if($tokens[2] == "hockey") {
						if(array_key_exists(3, $tokens)) {
							return hockey_stats($statsLink,$eventId)[$tokens[3]];
						}
					}
					$statName = $tokens[2];
					return loadLiveStatsDataCached($statsLink,$eventId)[$statName];
				} 
				else {
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
			$player = rpits_db_fetch_array($playerData);
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

function queryToJsonArray($query) {
	$result = dbquery($query);
	$columns = array();
	$rows = array();
	while ($row = rpits_db_fetch_assoc($result)) {
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
	$cacheRow = rpits_db_fetch_assoc($result);

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

		$headshotScript = file_get_contents('animations/headshot_2.js');
		$headshotScript = str_replace("SEQUENCE_REPLACEMENT_STRING", $animated_headshot_prefix . $title["team"] . '/' . $filename . '/' . $filename, $headshotScript);
		$headshotScript = str_replace("BACKGROUND_REPLACEMENT_STRING", realpath('out/' . $filename . '_noHeadshot.png'), $headshotScript);

		foreach($title['geos'] as $geo) {
			if($geo['name'] == 'headshot') {
				$headshotScript = str_replace("HEADSHOT_REPLACEMENT_STRING", realpath('cache/' . getGeoHash($geo) . '.tga'), $headshotScript);
			}
		}

		return $headshotScript;

	} 
	elseif($title["parent"] == 'templates/gameSummary.xml')
	{
		$filename = $title["name"] . $title["id"];
		$headshotScript = file_get_contents('test_3.js');
		$headshotScript = str_replace("BACKGROUND_REPLACEMENT_STRING", realpath('out/' . $filename . '.png'), $headshotScript);
		
		return $headshotScript;
	}
	elseif($title["parent"] == 'templates/sog_dropdown.xml')
	{
		$filename = $title["name"] . $title["id"];
		$headshotScript = file_get_contents('animations/sog_dropdown_1.js');
		$headshotScript = str_replace("BACKGROUND_REPLACEMENT_STRING", realpath('out/' . $filename . '.png'), $headshotScript);
		
		return $headshotScript;
	}
	elseif($title["parent"] == 'templates/home_offense_starters.xml')
	{
		$filename = $title["name"] . $title["id"];
		$headshotScript = file_get_contents('animations/starters_1.js');
		$headshotScript = str_replace("NO_TEXT_REPLACEMENT_STRING", realpath('out/' . $filename . '_no_text.png'), $headshotScript);
		$headshotScript = str_replace("BACKGROUND_REPLACEMENT_STRING", realpath('out/' . $filename . '.png'), $headshotScript);
		
		return $headshotScript;
	}
	elseif($title["parent"] == 'templates/home_defensive_starters.xml')
	{
		$filename = $title["name"] . $title["id"];
		$headshotScript = file_get_contents('animations/starters_1.js');
		$headshotScript = str_replace("NO_TEXT_REPLACEMENT_STRING", realpath('out/' . $filename . '_no_text.png'), $headshotScript);
		$headshotScript = str_replace("BACKGROUND_REPLACEMENT_STRING", realpath('out/' . $filename . '.png'), $headshotScript);
		
		return $headshotScript;
	}
	elseif($title["parent"] == 'templates/visitor_defensive_starters.xml')
	{
		$filename = $title["name"] . $title["id"];
		$headshotScript = file_get_contents('animations/starters_1.js');
		$headshotScript = str_replace("NO_TEXT_REPLACEMENT_STRING", realpath('out/' . $filename . '_no_text.png'), $headshotScript);
		$headshotScript = str_replace("BACKGROUND_REPLACEMENT_STRING", realpath('out/' . $filename . '.png'), $headshotScript);
		
		return $headshotScript;
	}
	elseif($title["parent"] == 'templates/visitor_offense_starters.xml')
	{
		$filename = $title["name"] . $title["id"];
		$headshotScript = file_get_contents('animations/starters_1.js');
		$headshotScript = str_replace("NO_TEXT_REPLACEMENT_STRING", realpath('out/' . $filename . '_no_text.png'), $headshotScript);
		$headshotScript = str_replace("BACKGROUND_REPLACEMENT_STRING", realpath('out/' . $filename . '.png'), $headshotScript);
		
		return $headshotScript;
	}
	else {
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
		
		/* Pull Random Stats */
		$venue_stats = $livestats->xpath("//venue");
		
		$cache["attendance"] = $venue_stats[0]["attend"]; 
		$cache["hid"] = $venue_stats[0]["homeid"];
		$cache["vid"] = $venue_stats[0]["visid"];
		$cache["date"] = $venue_stats[0]["date"];
		
		
		
	}
	return $cache;
}

?>
