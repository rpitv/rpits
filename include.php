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
?>