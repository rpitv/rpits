<?

include("include.php");

$event = $_GET["event"];
$team = $_GET["team"];
$thing = $_GET["thing"];

$result;
if ($thing == "billboards") {
	//echo("<h1>Not Implemented</h1>");
	$result = dbquery("SELECT * from billboards");
	while ($row = mysql_fetch_array($result)) {
		echo("<li type=\"billboard\" id=\"" . $row["id"] . "\"><img src=\"billboards/" . $row["file_name"] . "\" path=\"billboards/" . $row["file_name"] . "\" width=\"40\" />" . $row["title"] . "</li>\n");
	}
} else if ($event > 0) {
	$result = dbquery("SELECT *, event_title.id as etid, titles.name as title_name, titles.id as title_id FROM event_title LEFT JOIN titles on titles.id = event_title.title WHERE event_title.event = $event ORDER BY titles.id ASC");
	while ($row = mysql_fetch_array($result)) {
		echo("<li type=\"general\" id=\"" . $row["title_id"] . "\"><img src=\"thumbs/" . $row["name"] . $row["title_id"] . ".png\" path=\"out/" . $row["title_name"] . $row["title_id"] . ".png\" height=\"38\" />" . $row["title_name"] . "</li>\n");
	}
} else if ($event == -1) {
	$result = dbquery("SELECT *, titles.id as title_id, titles.name as title_name FROM titles");
	while ($row = mysql_fetch_array($result)) {
		echo("<li type=\"general\" id=\"" . $row["title_id"] . "\"><img src=\"thumbs/" . $row["name"] . $row["title_id"] . ".png\" path=\"out/" . $row["title_name"] . $row["title_id"] . ".png\" height=\"38\" />" . $row["title_name"] . "</li>\n");
	}
} else {
	//echo("<h1>Not Implemented</h1>");
	mysql_select_db("rpihockey");
	$result = dbquery("SELECT * from players WHERE team='$team' ORDER BY num ASC");

	while ($row = mysql_fetch_array($result)) {
		$num = $row["num"];
		$first = $row["first"];
		$last = $row["last"];
		$title_name = "$num - $first $last";
		$path = $num . $first . $last;
		$path = rawurlencode($path);
		echo("<li type=\"player\" id=\"" . $row["id"] . "\"><img path=\"out/" . $path . ".png\" src=\"thumbs/" . $path . ".png\" width=\"40\" />" . $title_name . "</li>\n");
	}
	mysql_select_db("rpits");
}
?>
