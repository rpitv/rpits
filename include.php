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

function listOfGeos($id) {
	$geos = array();
	$title = dbquery("SELECT * from titles left join templates on titles.template=templates.id where titles.id='$id'  ");
	$result = mysql_fetch_array($title);
	$templateXML = fopen($result["path"], "r");
	$contents = stream_get_contents($templateXML);
	$xml = new SimpleXMLElement($contents);
	foreach ($xml->geo->children() as $geo) {
		$name = (string) $geo["name"];
		$geos[$name] = $geo->getName();
	}
	foreach ($xml->overlay->children() as $geo) {
		$name = (string) $geo["name"];
		$geos[$name] = $geo->getName();
	}
	return $geos;
}

function dbFetchAll($id, $name) {
	$data = array();
	$title = dbquery("SELECT * from titles left join templates on titles.template=templates.id where titles.id='$id'  ");
	$result = mysql_fetch_array($title);
	$templateXML = fopen($result["path"], "r");
	$contents = stream_get_contents($templateXML);
	$xml = new SimpleXMLElement($contents);
	foreach ($xml->geo->children() as $geo) {
		if ($geo["name"] == $name) {
			$data = dbFetch($id, $geo);
		}
	}
	foreach ($xml->overlay->children() as $geo) {
		if ($geo["name"] == $name) {
			$data = dbFetch($id, $geo);
		}
	}
	return $data;
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

function dbFetch($id, $xml) {
	$data = array();
	foreach ($xml->attributes() as $key => $value) {
		$data[$key] = (string) $value;
	}
	$result = dbquery("SELECT * FROM cdb WHERE title_id=\"$id\" AND name=\"" . $data["name"] . "\";");
	while ($row = mysql_fetch_array($result)) {
		$data[$row["key"]] = $row["value"];
	}
	return $data;
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