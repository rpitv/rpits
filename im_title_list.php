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
  $result = dbquery("SELECT *, titles.id as title_id, titles.filename as Tfilename, templates.name as template_name, titles.name as title_name FROM titles JOIN templates ON titles.template = templates.id");
  while ($row = mysql_fetch_array($result)) {
    echo("<li type=\"general\" id=\"" . $row["title_id"] . "\"><img src=\"thumbs/" . $row["Tfilename"] . ".png\" path=\"out/" . $row["Tfilename"] . ".png\" height=\"38\" />" . $row["title_name"] . " (". $row["template_name"].")</li>\n");
  }
}
else
{
  //echo("<h1>Not Implemented</h1>");
  mysql_select_db("rpihockey");
  $result = dbquery("SELECT * from players WHERE team='$team' ORDER BY num ASC");
  
  while ($row = mysql_fetch_array($result))
  {
    $num = $row["num"];
    $first = $row["first"];
    $last = $row["last"];
    $title_name = "$num - $first $last";
    echo("<li type=\"player\" id=\"" . $row["id"] . "\"><img path=\"out/".$num.$first.$last .".png\" src=\"thumbs/".$num.$first.$last .".png\" width=\"40\" />" . $title_name . "</li>\n");
  }
  mysql_select_db("rpits");
}
?>
