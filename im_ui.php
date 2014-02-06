<link rel="stylesheet" type="text/css" href="im_ui.css" media="screen" />
<script src="js/lib/jquery-1.8.3.js" type="text/javascript"></script>
<script src="js/lib/jquery-ui-1.9.2.custom.js" type="text/javascript"></script>
<script src="js/lib/jquery.scrollintoview.js" type="text/javascript"></script>
<script src="im.js" type="text/javascript"></script>
<script src="im_ui.js" type="text/javascript"></script>
<script src="renderQueue.js" type="text/javascript"></script>

<?php
include("include.php");

$eventId = $_GET["eventId"];
?>
<script>
	ui.dbName = '<?= $mysql_database_name ?>';
</script>
<?
if (!$eventId) {
	?>
	<div id="eventSelector">
		<h2>Select an event</h2>
		<ul>
			<?
			$result = dbquery("SELECT * FROM events");
			while ($row = mysql_fetch_array($result)) {
				echo('<li><a href="im_ui.php?eventId=' . $row["id"] . '">' . $row["name"] . ' (' . $row["team1"] . ' vs. ' . $row["team2"] . ')</a></li>');
			}
			echo('<li><a href="#" id="editEvents">Edit / Add / Remove Events</a></li>');
			?>
		</ul>
		<div id="eventEditor"></div>
	</div>
	<?
} else {
  ?>
  <script>
  	ui.eventId = <?= $eventId ?>;
  </script>
  <?
	if ($eventId > 0) {
		$result = dbquery("SELECT * FROM events WHERE events.id = $eventId");
		$row = mysql_fetch_array($result);

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
		ui.dbName = '<?= $mysql_database_name ?>';
	</script>
	<div id="edit"></div>
	<div id="pane"></div>
	<div id="tabstrip"></div>
	<div id="input"><input type="text" /></div>
	<div id="actions"></div>
	<div id="renderQueue">
    <div class="label">Queue</div>
    <div id="queueMenu">
      <div id="process" class="queueMenuButton" onclick="window.renderQueue.processQueue(0, 0)">&#xe047;</div>
      <div id="prune" class="queueMenuButton" onclick="window.renderQueue.pruneQueue()">&#x2796;</div>
      <div id="destroy" class="queueMenuButton" onclick="window.renderQueue.destroyQueue()">&#x2713;</div>
    </div>    
  </div>

	<?php
}
?>
