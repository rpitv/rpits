<?

include ("init.php");
mysql_select_db("rpits");

function dbquery($query) {
	$result = mysql_query($query) or die("<b>Error with MySQL Query:</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
	return $result;
}

function dbqueryl($query) {
	$result = mysql_query($query);
	return $result;
}

function getTitle($id) {

	$titleResult = dbquery("SELECT * from titles where id=\"$id\" LIMIT 1;");
	$title = mysql_fetch_assoc($titleResult);

	// null checking

	if(is_numeric($title["parent"])) {
		$parent = getTitle($title["parent"]);
	} else {
		$parent = getTitleFromXML($title["parent"]);
	}

	$title['geos'] = $parent['geos'];
	$title['parentName'] = $parent['name'];

	$cdbResult = dbquery("SELECT * FROM cdb WHERE title_id=\"$id\";");
	while ($row = mysql_fetch_array($cdbResult)) {
		$title['geos'][$row['name']][$row['key']] = $row['value'];
	}

	foreach($title['geos'] as $key=>$geo) {
		$title['geos'][$key] = tokenReplace($geo);
	}

	return $title;
}

function getTitleFromXML($path) {
	$file = fopen($path, "r");
	$contents = stream_get_contents($file);
	$xml = new SimpleXMLElement($contents);

	$title['name'] = (string) $xml->name;
	
	$title['geos'] = getAllChildren($xml->geos);

	return $title;
}

function getAllChildren($xml) {
	$children = [];
	$i = 0;
	foreach($xml->children() as $childXML) {
		$child = getAttributes($childXML);
		if($child['order']) die("Attribute 'order' is illegal in RML");
		$child['order'] = $i;
		$children[$child['name']] = $child;
		$i++;
	}
	return $children;
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
	$teamResource = dbQuery("SELECT * FROM teams WHERE abbrev='$team'");
	$teamRow = mysql_fetch_assoc($teamResource);

	$orgResource = dbQuery("SELECT * FROM organizations WHERE code='" . $teamRow['org'] . "'");
	$orgRow = mysql_fetch_assoc($orgResource);

	return array_merge($teamRow,$orgRow);
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
	if($node['type']) die("Attribute 'type' is illegal in RML");
	if($node['value']) die("Attribute 'value' is illegal in RML");
	$node['type'] = $xml->getName();
	if((string) $xml) {
		$node['value'] = (string) $xml;
	}
	return $node;
}

function tokenReplace($data) {
	foreach($data as $key => $string) {
		$matches = array();

		preg_match_all('/\{(.*?)\}/', $string, $matches);
		$tokens = $matches[1];
		foreach($tokens as $token) {
			$string = preg_replace('/\{'.$token.'\}/',getToken($token),$string);
		}
		$data[$key] = $string;
	}
	return $data;
}

// This is hardcoded and not ideal, but it will suffice for now
// and become a more open standard later
// example: {e.h.color} is the home team's color for event 1
function getToken($token) {
	$tokens = explode('.',$token);

	if($tokens[0] != 'e' && $tokens[0] != 'event')
		return '';

	$eventId = 1; // bad
	$eventResource = dbQuery("SELECT * FROM events WHERE id='$eventId'");
	$eventRow = mysql_fetch_assoc($eventResource);

	$teamColumn = $tokens[1] == 'h' ? 'team1' : 'team2';

	$teamRow = fetchTeam($eventRow[$teamColumn]);

	$teamRow['logo'] = 'teamlogos/' . $teamRow['logo'];

	return $teamRow[$tokens[2]];
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

?>