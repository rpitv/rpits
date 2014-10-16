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

function addGeoToCanvas($canvas,$geo,$bustCache = false) {
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
			$teamColumn = $tokens[1] == 'h' ? 'team1' : 'team2';
			$newToken = 't.' . $eventRow[$teamColumn] . '.' . implode('.', array_slice($tokens, 2));
			return getToken($newToken, $eventId);
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

	// TEMPORARY: FIGURE OUT A BETTER LOCATION FOR THIS OR MOVE TO config.php
	$animated_headshot_prefix = '/var/www/machac3/rpits/anim_heads/';

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

?>
