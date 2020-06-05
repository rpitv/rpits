<?

include("include.php");

$event = $_GET["event"] ?? '';
$team = $_GET["team"] ?? '';
$thing = $_GET["thing"] ?? '';
$format = $_GET["format"] ?? '';

$checkHash = $_GET["checkHash"] ?? '';

if(isset($_POST["saveEvent"])) {
	echo '<pre>';
	print_r($_POST);
	foreach($_POST['sort'] as $title) {
		dbquery('UPDATE event_title SET `sort`="' . $title['sort'] . '" WHERE `title`="'.$title['id'].'" AND `event`="'.$_POST['saveEvent'].'"');
	}
	exit();
}

if($checkHash) {
	$list = array();
	if($event > 0) {
		$titles = queryAssoc("SELECT * FROM event_title WHERE `event`='$event' ORDER BY title ASC");
		foreach($titles as $titleRow) {
			$title = getTitle($titleRow['title'],$event);
			if($title) {
				$list[$title['id']] = checkHashForTitle($title);
			}
		}
	} else if ($team) {
		// Player hash has yet to be standardized, so return all players as true for now
		$players = queryAssoc("SELECT * from players WHERE team='$team' ORDER BY num ASC");
		foreach($players as $player) {
			$list[$player['id']] = false;
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
	while ($row = $result->fetch_array()) {
		echo("<li type=\"billboard\" id=\"" . $row["id"] . "\"><img src=\"billboards/" . $row["file_name"] . "\" path=\"billboards/" . $row["file_name"] . "\" width=\"40\" />" . $row["title"] . "</li>\n");
	}
} else if ($event > 0) {
	$result = dbquery("SELECT * FROM event_title WHERE `event`='$event' ORDER BY `sort`,`title` ASC");
	while ($row = $result->fetch_assoc()) {
		$title = getTitle($row['title'],$event);
		if($title && $format == 'json') {
			$title['sort'] = $row['sort'];
			$list[] = $title;
		} else if($title) {
			echo("<li type=\"general\" id=\"" . $title["id"] . "\"><img src=\"thumbs/" . $title["name"] . $title["id"] . ".png\" path=\"out/" . $title["name"] . $title["id"] . ".png\" height=\"38\" />" . $title["name"] . "</li>\n");
		}
	}
} else if ($event == -1) {
	$result = dbquery("SELECT *, titles.id as title_id, titles.name as title_name FROM titles");
	while ($row = $result->fetch_array()) {
		echo("<li type=\"general\" id=\"" . $row["title_id"] . "\"><img src=\"thumbs/" . $row["name"] . $row["title_id"] . ".png\" path=\"out/" . $row["title_name"] . $row["title_id"] . ".png\" height=\"38\" />" . $row["title_name"] . "</li>\n");
	}
} else {
	//echo("<h1>Not Implemented</h1>");
	$result = dbquery("SELECT * from players WHERE team='$team' ORDER BY num ASC");

	while ($row = $result->fetch_array()) {
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
