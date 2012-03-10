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
?>