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
		ui.dbName = <?= $mysql_database_name ?>;
	</script>
	<div id="edit"></div>
	<div id="pane">
	  <ul class="titles active" request="im_title_list.php?event=<?= $eventId ?>"><div id="add-title"></div></ul>
	  <ul class="titles" request="im_title_list.php?team=<?= $team1 ?>"></ul>
	  <ul class="titles" request="im_title_list.php?team=<?= $team2 ?>"></ul>
	<? if ($team3) { ?><ul class="titles" request="im_title_list.php?team=<?= $team3 ?>"></ul><? } ?>
	  <ul class="titles" request="im_title_list.php?thing=billboards"></ul>
	</div>
	<div id="tabstrip">
	<!--<span id = "help" style = "font-size:11px"> Up/Down - Select; Left/Right - Tab; E - Edit; R - Preview; Q - Queue; Space - Bring Up/Down;
		F - Force Render; U - Update All; C - Cut</span>-->
	  <!--<div class="tab active" request="im_title_list.php">All Titles</div>-->
	  <div class="tab active" type="general" request="im_title_list.php?event=<?= $eventId ?>" tid="0"><?= $eventName ?> Titles</div>
	  <div class="tab" type="player" request="im_title_list.php?team=<?= $team1 ?>" tid="1"><?= $team1 ?> Players</div>
	  <div class="tab" type="player" request="im_title_list.php?team=<?= $team2 ?>" tid="2"><?= $team2 ?> Players</div>
		<? if ($team3) { ?><div class="tab" request="im_title_list.php?team=<?= $team3 ?>" tid="3"><?= $team3 ?> Players</div> <? } ?>
	  <div class="tab" type="billboards" request="im_title_list.php?thing=billboards" tid="4">Billboards</div>
		<div id="updateAllContainer">
      <button id="updateAll">Update All</button>
      <label>Force:<input id="updateAllForce" type="checkbox" value="true" /></label>
	  </div>
  </div>
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
