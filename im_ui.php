<link rel="stylesheet" type="text/css" href="im_ui.css" media="screen" />
<script src="js/lib/jquery-1.8.3.js" type="text/javascript"></script>
<script src="js/lib/jquery-ui-1.9.2.custom.js" type="text/javascript"></script>
<script src="js/lib/jquery.scrollintoview.js" type="text/javascript"></script>
<script src="js/jscolor/jscolor.js" type="text/javascript"></script>
<script src="im.js" type="text/javascript"></script>
<script src="im_ui.js" type="text/javascript"></script>
<script src="renderQueue.js" type="text/javascript"></script>

<?php
include("include.php");

$eventId = $_GET["eventId"];

$bug_state_json = @file_get_contents($bug_keyer_url . 'state');

if ($bug_state_json !== FALSE) {
	$bug_info = json_decode($bug_state_json);
	if ($bug_info->state === 'down') {
?>
		<script type="text/javascript">
			$(document).ready( function() {
				$('.bug').hide();
			});
		</script>
<?php
	}
}
?>
<?php
if (!$eventId) {
?>
	<div id="eventSelector">
		<h2>Select an event</h2>
		<ul>
<?php
			$result = dbquery("SELECT * FROM events");
			while ($row = rpits_db_fetch_array($result)) {
				echo('<li><a href="im_ui.php?eventId=' . $row["id"] . '">' . $row["name"] . ' (' . $row["team1"] . ' vs. ' . $row["team2"] . ')</a></li>');
			}
			echo('<li><a href="#" id="editEvents">Edit / Add / Remove Events</a></li>');
?>
		</ul>
		<div id="eventEditor"></div>
	</div>
<?php
} else {
?>
	<script>
		ui.eventId = <?= $eventId ?>;
	</script>
<?php
	if ($eventId > 0) {
		$result = dbquery("SELECT * FROM events WHERE events.id = $eventId");
		$row = rpits_db_fetch_array($result);

		$eventName = $row["name"];
		$team1 = $row["team1"];
		$team2 = $row["team2"];
		//$team3 = "career";
	} else {
		$eventName = "Football"; // EDIT THIS LINE
		$team1 = "rpif";
		$team2 = "hobartf";
	}
?>
	<script>
		ui.eventId = <?= $eventId ?>;
	</script>
	<div id="edit"></div>
	<div id="pane"></div>
	<div id="tabstrip"></div>
	<div id="input"><input type="text" /></div>
	<div id="actions"></div>
	<div id="renderQueue">
		<div class="label">Queue</div>
		<div id="queueMenu">
			<div id="process" class="queueMenuButton" onclick="window.renderQueue.processQueue()"><div>&#xe047;</div></div>
			<div id="prune" class="queueMenuButton" onclick="window.renderQueue.pruneQueue()"><div>&#x2796;</div></div>
			<div id="destroy" class="queueMenuButton" onclick="window.renderQueue.destroyQueue()"><div>&#x2713;</div></div>
		</div>    
	</div>
<?php
}
?>

<!--select for adding titles-->
<select hidden id="addSelect" style="max-width:40px; margin-left:2px;" title="Select a title template to add a new title to this event.">
	<?php
		//add blank
		echo '<option value="+">+</option>';
		$templates = glob("templates/*.xml");
		foreach ($templates as $template) {
			echo '<option value="'.$template.'">'.$template.'</option>';
		}
	?>
</select>
	
	
	
