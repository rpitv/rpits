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
function getContent($id,$field)
{
  $query = "SELECT * FROM cdb WHERE title_id='$id' AND field='$field' LIMIT 1";
  $result = dbquery($query);
  $assoc = mysql_fetch_assoc($result);
  return $assoc["data"];
}
function getColor($id,$field,$im)
{
  $data = getContent($id,$field);
  $rgb = explode(",",$data);
  $color = imagecolorallocate($im, $rgb[0], $rgb[1], $rgb[2]);
  return $color;
}
function getFont($font)
{
  $query = "SELECT * FROM fonts WHERE name='$font' LIMIT 1";
  $result = dbquery($query);
  $assoc = mysql_fetch_assoc($result);
  return $assoc["path"];
}
?>