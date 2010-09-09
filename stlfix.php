<?php

mysql_connect("localhost","root","");
mysql_select_db("rpihockey");

$query = "SELECT * FROM players WHERE team='stl'";
$result = mysql_query($query) or die("<b>YOU DID SOMETHING WRONG YOU IDIOT</b>.\n<br />Query: $query<br />\nError: (" . mysql_errno() . ") " . mysql_error());
while($row = mysql_fetch_array($result))
{
  $id = $row["id"];
  $height = $row["year"];
  $year = $row["height"];
  $query = "UPDATE players SET height='$height',year='$year' WHERE id='$id'";
  $result2 = mysql_query($query) or die("<b>YOU DID SOMETHING WRONG YOU IDIOT</b>.\n<br />Query: $query<br />\nError: (" . mysql_errno() . ") " . mysql_error());
  echo("Updated " . $row["first"] . " " . $row["last"] . "<br>\n");
}



?>
