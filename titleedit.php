<title>General Title Editor - List</title>

<?
mysql_connect("localhost","root","");
mysql_select_db("rpihockey");

$id = $_GET["id"];
$filename = $_GET["filename"];
$title = $_GET["title"];
$content = $_GET["content"];
$team = $_GET["team"];
$logo = $_GET["logo"];
$height = $_GET["height"];
$halfwidth = $_GET["half-width"];
$special = $_GET["special"];
$update = $_GET["update"];
$add = $_GET["add"];
$col1 = $_GET["col1"];
$col2 = $_GET["col2"];
$col3 = $_GET["col3"];
$team2 = $_GET["team2"];
$new = $_GET["new"];

if($add == 'Add') {
  $query = "INSERT INTO `general` (`filename`, `title`, `content`, `team`, `logo`, `height`, `half-width`, `special`) VALUES ('$filename', '$title', '$content', '$team', '$logo', '$height', '$halfwidth', '$special');";
  $result = mysql_query($query) or die("<b>YOU DID SOMETHING WRONG YOU IDIOT</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
}
if($update == 'Update') {
  $query = "UPDATE `general` SET `filename`='$filename', `title`='$title', `content`='$content', `team`='$team', `logo`='$logo', `height`='$height', `half-width`='$halfwidth', `special`='$special', `col1` = '$col1', `col2`='$col2', `col3`='$col3', `team2`='$team2' WHERE `id`='$id' ;";
  $result = mysql_query($query) or die("<b>YOU DID SOMETHING WRONG YOU IDIOT</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
}
if($id || $add || $new) {
  if($id)
  {
    $query = "SELECT * from general WHERE id = $id";
    $result = mysql_query($query) or die("<b>YOU DID SOMETHING WRONG YOU IDIOT</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
    $row = mysql_fetch_array($result);
  }
  else if($add) {
    $query = "SELECT * from general WHERE filename = '$filename'";
    $result = mysql_query($query) or die("<b>YOU DID SOMETHING WRONG YOU IDIOT</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
    $row = mysql_fetch_array($result);
  }
  ?>
<div style="height:480px;width:640px;position:relative">
  <div style="position:absolute;height:480px;width:43px;background-color:#FF7722;opacity:.3;left:0px;top:0px;"></div>
  <div style="position:absolute;height:480px;width:43px;background-color:#FF7722;opacity:.3;right:0px;top:0px;"></div>
  <div style="position:absolute;height:25px;width:640px;background-color:#FF7722;opacity:.3;left:0px;top:0px;"></div>
  <div style="position:absolute;height:25px;width:640px;background-color:#FF7722;opacity:.3;left:0px;bottom:0px;"></div>
<img style="position:absolute;bottom:0px;<? if($row["height"] < 455){?>margin-bottom:25px<? } ?>" width="640" src="gentitle.php?id=<? echo($row["id"]); ?>" />
</div>
  <?
  echo("<div style\"clear:both\"><form action\"titleedit.php\" \"method=\"GET\"><input type=\"hidden\" name=\"id\" value=\"" . $row["id"] . "\">");
  echo("Filename: <input type=text name=\"filename\" size=15 value=\"" . $row["filename"] . "\"><br/>");
  echo("Title: <input type=text name=\"title\" size=30 value=\"" . $row["title"] . "\"><br/>");
  if($row["special"] == "4" ) {
    echo("<textarea name=\"content\" rows=6 cols=5>" . $row["content"] . "</textarea><textarea name=\"col1\" rows=6 cols=5>" . $row["col1"] . "</textarea><textarea name=\"col2\" rows=6 cols=25>" . $row["col2"] . "</textarea><textarea name=\"col3\" rows=6 cols=5>" . $row["col3"] . "</textarea><br/>");
  } elseif($row["special"] == "5") {
    echo("<textarea name=\"content\" rows=1 cols=20>\n" . $row["content"] . "</textarea><textarea name=\"col1\" rows=1 cols=5>" . $row["col1"] . "</textarea><br><textarea name=\"col2\" rows=1 cols=20>" . $row["col2"] . "</textarea><textarea name=\"col3\" rows=1 cols=5>" . $row["col3"] . "</textarea><br/>");
  } elseif($row["special"] == "3") {
    echo("<textarea name=\"content\" rows=13 cols=15>\n" . $row["content"] . "</textarea><textarea name=\"col1\" rows=13 cols=8>" . $row["col1"] . "</textarea><textarea name=\"col2\" rows=13 cols=7>" . $row["col2"] . "</textarea><textarea name=\"col3\" rows=13 cols=8>" . $row["col3"] . "</textarea><br/>");
  } elseif($row["special"] == "2") {
    echo("<textarea name=\"content\" rows=7 cols=10>" . $row["content"] . "</textarea><textarea name=\"col1\" rows=7 cols=15>\n" . $row["col1"] . "</textarea><textarea name=\"col2\" rows=7 cols=15>" . $row["col2"] . "</textarea><br/>");
  } elseif($row["special"] == "1") {
    echo("<textarea name=\"content\" rows=1 cols=45>\n" . $row["content"] . "</textarea><br><textarea name=\"col1\" rows=1 cols=45>" . $row["col1"] . "</textarea><br><textarea name=\"col2\" rows=1 cols=45>" . $row["col2"] . "</textarea><br><textarea name=\"col3\" rows=1 cols=45>" . $row["col3"] . "</textarea><br/>");
  } else {
    echo("<textarea name=\"content\" rows=2 cols=45>" . $row["content"] . "</textarea><br/>");
  }
  if($row["special"] == "2" ||$row["special"] == "1" || $row["special"] == "5") {
    echo("Teams: <input type=text name=\"team\" size=6 value=\"" . $row["team"] . "\"><input type=text name=\"team2\" size=6 value=\"" . $row["team2"] . "\"><br/>");
  } else {
    echo("Team: <input type=text name=\"team\" size=6 value=\"" . $row["team"] . "\"><br/>");
  }
  echo("Logo: <input type=text name=\"logo\" size=1 value=\"" . $row["logo"] . "\"><br/>");
  echo("Height: <input type=text name=\"height\" size=3 value=\"" . $row["height"] . "\"> - Y pos: ");
  if($row["height"] != 480)
    echo(480-$row["height"]-20);
  else
    echo(0);
  echo("<br/>");
  echo("Half-Width: <input type=text name=\"half-width\" size=1 value=\"" . $row["half-width"] . "\"><br/>");
  echo("Special: <input type=text name=\"special\" size=1 value=\"" . $row["special"] . "\"><br/>");
  if($new)
    echo("<input type=\"submit\" name=\"add\" value=\"Add\">");
  else
    echo("<input type=\"submit\" name=\"update\" value=\"Update\">");
  echo("</form>");
  echo("</div>");
}
?>