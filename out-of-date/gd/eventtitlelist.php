<form>
  <select name="event">
<?

mysql_connect("localhost","root","");
mysql_select_db("rpihockey");

$query = "SELECT * FROM current_settings LIMIT 1";
$result = mysql_query($query) or die("<b>YOU DID SOMETHING WRONG YOU IDIOT</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
$current = mysql_fetch_array($result);
$current_event = $current["event_id"];

if($_GET["action"] == "add")
{
  $title_id = $_GET["title_id"];
  $query = "INSERT INTO event_title (event_id,title_id) VALUES ($current_event,$title_id)";
  $result = mysql_query($query);
}
if($_GET["action"] == "remove")
{
  $et_id = $_GET["et_id"];
  $query = "DELETE FROM event_title WHERE id=$et_id";
  $result = mysql_query($query);
}

$query = "SELECT * FROM event";
$result = mysql_query($query) or die("<b>YOU DID SOMETHING WRONG YOU IDIOT</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
while($row = mysql_fetch_array($result))
{
  if($row["id"] == $current_event)
    echo("<option selected=\"selected\">");
  else
    echo("<option>");
  echo($row["name"] . "</option>\n");
}
?>
  </select>
  <input type="submit" name="submit" value="Set Active Event" /><a href="generaltitle.php?shrink=1" target="_parent">Shrink</a>
</form>

<?
$query = "SELECT *,event_title.id as etid from event_title LEFT JOIN general ON general.id = event_title.title_id WHERE event_title.event_id='$current_event'";
$result = mysql_query($query) or die("<b>YOU DID SOMETHING WRONG YOU IDIOT</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
while($row = mysql_fetch_array($result))
{
  echo("<div>");
  echo("<a href=\"eventtitlelist.php?action=remove&et_id=".$row["etid"] . "\">" . $row["filename"] . " - " . $row["title"] . "</a>");
  echo("</div>");
}
?>
<hr>
<?
$query = "SELECT * FROM general WHERE general.id NOT IN
(SELECT general.id from event_title LEFT JOIN general ON general.id = event_title.title_id WHERE event_title.event_id='$current_event')";
$result = mysql_query($query) or die("<b>YOU DID SOMETHING WRONG YOU IDIOT</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
while($row = mysql_fetch_array($result))
{
  echo("<div>");
  echo("<a href=\"eventtitlelist.php?action=add&title_id=".$row["id"] . "\">" . $row["filename"] . " - " . $row["title"] . "</a>");
  echo("</div>");
}
?>