<?php

mysql_connect("localhost","root","");
mysql_select_db("rpihockey");

$query = "SELECT * FROM players WHERE team='rpimb'";
$result = mysql_query($query) or die("<b>YOU DID SOMETHING WRONG YOU IDIOT</b>.\n<br />Query: $query<br />\nError: (" . mysql_errno() . ") " . mysql_error());
while($row = mysql_fetch_array($result))
{
  $id = $row["id"];
  $s1 = $row["stype"];
  $s2 = $row["s1"];
  $s3 = $row["s2"];
  $s4 = $row["s3"];
  $s5 = $row["s4"];
  $stype = "bb";
  $query = "UPDATE players SET s1='$s1',s2='$s2',s3='$s3',s4='$s4',s5='$s5',stype='$stype' WHERE id='$id'";
  $result2 = mysql_query($query) or die("<b>YOU DID SOMETHING WRONG YOU IDIOT</b>.\n<br />Query: $query<br />\nError: (" . mysql_errno() . ") " . mysql_error());
  echo("Updated " . $row["first"] . " " . $row["last"] . "<br>\n");
}
  


?>
