<?
mysql_connect("localhost","root","");
mysql_select_db("rpits");

function dbquery($query)
{
  $result = mysql_query($query) or die("<b>Error with MySQL Query:</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
  return $result;
}
function dbqueryl($query)
{
  $result = mysql_query($query);
  return $result;
}

function dbFetch($id,$xml)
{ 
  $data = array();
  foreach($xml->attributes() as $key=>$value)
    $data[$key] = (string)$value;
  $result = dbquery("SELECT * FROM cdb WHERE title_id=\"$id\" AND name=\"".$data["name"]."\";");
  while($row = mysql_fetch_array($result))
    $data[$row["key"]] = $row["value"];
  return $data;
}

function rgbhex($red, $green, $blue) {
    $red = 0x10000 * max(0,min(255,$red+0));
    $green = 0x100 * max(0,min(255,$green+0));
    $blue = max(0,min(255,$blue+0));
    return "#".str_pad(strtoupper(dechex($red + $green + $blue)),6,"0",STR_PAD_LEFT);
}
?>