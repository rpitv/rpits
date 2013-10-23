<?

include("include.php");

$event = $_GET["event"];
$team = $_GET["team"];
$thing = $_GET["thing"];
$format = $_GET["format"];

$checkHash = $_GET["checkHash"];

if($checkHash) {
	$list = array();
	if($event > 0) {
		$titles = queryAssoc("SELECT * FROM event_title WHERE `event`='$event' ORDER BY title ASC");
		foreach($titles as $titleRow) {
			$title = getTitle($titleRow['title']);
			if($title) {
				$list[$title['id']] = checkHashForTitle($title);
			}
		}
	} else if ($team) {
		// Player hash has yet to be standardized, so return all players as true for now
		$players = queryAssoc("SELECT * from players WHERE team='$team' ORDER BY num ASC");
		foreach($players as $player) {
			$list[$player['id']] = true;
		}
	}
	echo json_encode($list);
	exit();
}

$list = array();

$result;
if ($thing == "billboards") {
	//echo("<h1>Not Implemented</h1>");
	$result = dbquery("SELECT * from billboards");
	while ($row = mysql_fetch_array($result)) {
		echo("<li type=\"billboard\" id=\"" . $row["id"] . "\"><img src=\"billboards/" . $row["file_name"] . "\" path=\"billboards/" . $row["file_name"] . "\" width=\"40\" />" . $row["title"] . "</li>\n");
	}
} else if ($event > 0) {
	$result = dbquery("SELECT * FROM event_title WHERE `event`='$event' ORDER BY title ASC");
	while ($row = mysql_fetch_assoc($result)) {
		$title = getTitle($row['title']);
		if($title && $format == 'json') {
			$list[] = $title;
		} else if($title) {
			echo("<li type=\"general\" id=\"" . $title["id"] . "\"><img src=\"thumbs/" . $title["name"] . $title["id"] . ".png\" path=\"out/" . $title["name"] . $title["id"] . ".png\" height=\"38\" />" . $title["name"] . "</li>\n");
		}
	}
} else if ($event == -1) {
	$result = dbquery("SELECT *, titles.id as title_id, titles.name as title_name FROM titles");
	while ($row = mysql_fetch_array($result)) {
		echo("<li type=\"general\" id=\"" . $row["title_id"] . "\"><img src=\"thumbs/" . $row["name"] . $row["title_id"] . ".png\" path=\"out/" . $row["title_name"] . $row["title_id"] . ".png\" height=\"38\" />" . $row["title_name"] . "</li>\n");
	}
} else {
	//echo("<h1>Not Implemented</h1>");
	$result = dbquery("SELECT * from players WHERE team='$team' ORDER BY num ASC");

	while ($row = mysql_fetch_array($result)) {
		$num = $row["num"];
		$first = $row["first"];
		$last = $row["last"];
		$title_name = "$num - $first $last";
		$path = $num . $first . $last;
		$path = rawurlencode($path);
		if($format == 'json') {
			$list[] = getStatscard($row['id']);
		} else {
			echo("<li type=\"player\" id=\"" . $row["id"] . "\"><img path=\"" . $path . ".png\" src=\"thumbs/" . $path . ".png\" width=\"40\" />" . $title_name . "</li>\n");
		}
	}
}

if($format == 'json') {
	echo json_encode($list);
}
?>
