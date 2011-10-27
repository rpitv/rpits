<?

mysql_connect("localhost", "root", "");
mysql_select_db("rpihockey");

$event = $_GET["event"];
$team = $_GET["team"];

$result;
if(!$team)
{
  if ($event == 0)
    $query = "SELECT * from general";
  if ($event > 0)
    $query = "SELECT *,event_title.id as etid from event_title LEFT JOIN general ON general.id = event_title.title_id WHERE event_title.event_id='$event'";
  $result = mysql_query($query) or die("<b>YOU DID SOMETHING WRONG YOU IDIOT</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
  while ($row = mysql_fetch_array($result)) {
    echo("<li type=\"general\" id=\"" . $row["id"] . "\"><img src=\"gentitle.php?id=" . $row["id"] . "\" width=\"40\" />" . $row["filename"] . "</li>\n");
  }
}
else
{
  $query = "SELECT * from players WHERE team='$team' ORDER BY num ASC";
  $result = mysql_query($query) or die("<b>YOU DID SOMETHING WRONG YOU IDIOT</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
  while ($row = mysql_fetch_array($result))
  {
    $num = $row["num"];
    $first = $row["first"];
    $last = $row["last"];
    $title_name = "$num - $first $last";
    echo("<li type=\"player\" id=\"" . $row["id"] . "\"><img src=\"statscard.php?id=" . $row["id"] . "\" width=\"40\" />" . $title_name . "</li>\n");
  }
}
?>
