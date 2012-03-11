<?

include("include.php");

$event = $_GET["event"];
$team = $_GET["team"];
$thing = $_GET["thing"];

$result;
if($thing == "billboards")
{
  echo("<h1>Not Implemented</h1>");
  /*$result = dbquery("SELECT * from billboards");
  while ($row = mysql_fetch_array($result)) {
    echo("<li type=\"billboard\" id=\"" . $row["id"] . "\"><img src=\"billboards\\" . $row["file_name"] . "\" width=\"40\" />" . $row["title"] . "</li>\n");
  }*/
}
else if(!$team)
{
  $result = dbquery("SELECT *, templates.name as template_name, titles.name as title_name FROM titles RIGHT JOIN templates ON titles.template = templates.id");
  while ($row = mysql_fetch_array($result)) {
    echo("<li type=\"general\" id=\"" . $row["id"] . "\"><img src=\"out/" . $row["filename"] . ".png\" height=\"38\" />" . $row["title_name"] . " (". $row["template_name"].")</li>\n");
  }
}
else
{
  echo("<h1>Not Implemented</h1>");
  /*
  $result = dbquery("SELECT * from players WHERE team='$team' ORDER BY num ASC");
  while ($row = mysql_fetch_array($result))
  {
    $num = $row["num"];
    $first = $row["first"];
    $last = $row["last"];
    $title_name = "$num - $first $last";
    echo("<li type=\"player\" id=\"" . $row["id"] . "\"><img src=\"statscard.php?id=" . $row["id"] . "\" width=\"40\" />" . $title_name . "</li>\n");
  }*/
}
?>
