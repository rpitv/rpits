<link rel="stylesheet" type="text/css" href="im_ui.css" media="screen" />
<script src="js/jquery-1.8.3.js" type="text/javascript"></script>
<script src="js/jquery-ui-1.9.2.custom.js" type="text/javascript"></script>
<script src="js/jquery.scrollintoview.js" type="text/javascript"></script>
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

	<div id="program"><div class="label">Program</div><div class="image"></div></div>
	<div id="preview"><div class="label">Preview</div><div class="image"></div></div>
	<div id="edit"></div>
	<div id="pane">
	  <ul class="titles active" request="im_title_list.php?event=<?= $eventId ?>"><div id="add-title"></div></ul>
	  <ul class="titles" request="im_title_list.php?team=<?= $team1 ?>"></ul>
	  <ul class="titles" request="im_title_list.php?team=<?= $team2 ?>"></ul>
	<? if ($team3) { ?><ul class="titles" request="im_title_list.php?team=<?= $team3 ?>"></ul><? } ?>
	  <ul class="titles" request="im_title_list.php?thing=billboards"></ul>
	</div>
	<div id="tabstrip">
	  <!--<div class="tab active" request="im_title_list.php">All Titles</div>-->
	  <div class="tab active" request="im_title_list.php?event=<?= $eventId ?>" tid="0"><?= $eventName ?> Titles</div>
	  <div class="tab" request="im_title_list.php?team=<?= $team1 ?>" tid="1"><?= $team1 ?> Players</div>
	  <div class="tab" request="im_title_list.php?team=<?= $team2 ?>" tid="2"><?= $team2 ?> Players</div>
		<? if ($team3) { ?><div class="tab" request="im_title_list.php?team=<?= $team3 ?>" tid="3"><?= $team3 ?> Players</div> <? } ?>
	  <div class="tab" request="im_title_list.php?thing=billboards" tid="4">Billboards</div>
	</div>
	<div id="input"><input type="text" /></div>
	<div id="actions"></div>
	<div id="log"></div>
	<div id="loadtarget"></div>
	<div id="renderQueue">
    <div class="label">Queue</div>
    <button id="test" name="test" style="margin-bottom:10px;bottom:10px;" onclick="window.renderQueue.addToQueue('string1')">Test</button>
    <button id="prune" name="prune" style="margin-bottom:10px;bottom:10px;" onclick="window.renderQueue.pruneQueue()">Prune</button>
    <button id="process" name="process" style="margin-bottom:10px;bottom:10px;" onclick="window.renderQueue.processQueue(0)">Process</button>
    <button id="destroy" name="destroy" style="margin-bottom:10px;bottom:10px;" onclick="window.renderQueue.destroyQueue()">Destroy</button>
  </div>

<script type="text/javascript"> // Used for page leave checking
  var eventIdNum = <?= $eventId ?>;
</script>

	<?php
}
?>
