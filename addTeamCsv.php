<?php
header('Content-Type: text/html; charset=utf-8');
?>
<title>Team Roster Adder</title>

<script src="./js/lib/jquery-1.8.3.js"></script>
<script src="./parseRoster.js"></script>
<script src="./state_province.js"></script>

<h1>Add Team Roster</h1>

<?php
include ("init.php");
include ("include.php");
ini_set("auto_detect_line_endings", true);
$team_sel = $_POST["team_sel"];
$csv = $_POST["csv"];
$archive = $_POST["archive"];
$chs_prefix = $_GET["pull_url"];
$SIDEARM_url = $_GET["sidearm_url"];

//////////////////// CHS Pulling
if ($_GET['pull_url']) {
	if (date('n')>8) {
		$season = date('y') . (date('y')+1);
	} else {
		$season = (date('y')-1) . date('y');
	}

	$chs = fopen("http://www.collegehockeystats.net/". $season ."/rosters/" . $chs_prefix, "r");
	$roster = stream_get_contents($chs);
	$roster = mb_convert_encoding($roster, 'UTF-8', 'ASCII');
	$roster = str_replace("\xc2\x9a", "\xc5\xa1" , $roster); // replace incorrect "Single Character Introducer" with "Small Latin S with Caron"
	$roster = addslashes($roster);
	$roster = str_replace([chr(10), chr(13)], '', $roster);  // fix newline issues
	$roster = stristr($roster, "<TABLE BORDER"); // get only the first roster
	$roster = substr($roster, 0, (stripos($roster, "</TABLE>")+8));

	$chs_stats = fopen("http://www.collegehockeystats.net/". $season ."/teamstats/" . $chs_prefix, "r");
	$stats = stream_get_contents($chs_stats);
	$stats = mb_convert_encoding($stats, 'UTF-8', 'ASCII');
	$stats = addslashes($stats);
	$stats = str_replace([chr(10), chr(13)], '', $stats);  // fix newline issues
	$stats = stristr($stats, "<table width=\\\"856"); // get only stat table data
	$stats = substr($stats, 0, (strripos($stats, "</TABLE><HR")+8));

	$result = mysql_query("SELECT * FROM teams WHERE chs_abbrev='$chs_prefix'");
	$team_preset = mysql_fetch_assoc($result);

?> 

	<script>
	$(document).ready( function() {
		var content_roster = "<?= $roster ?>";
		var content_stat = "<?= $stats ?>";
		
		//$("#CHSabbr, #parseTableHTML, #boxSIDEARM").hide()  // hide unneeded things
		
		parse_table_HTML(content_roster, content_stat);
		$("#team_box").val("<?= $team_preset['player_abbrev'] ?>");
	});
	</script>

<?php
}

//////////////////// SIDEARM Pulling
if ($_GET['sidearm_url']) {
	$opts = array('http' =>
		array(
			'method' => 'GET',
			'protocol_version' => '1.1',
			'header' => 'User-Agent: Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.137 Safari/537.36'
		)
	);
	$context = stream_context_create($opts);
	$side = fopen(rawurldecode($SIDEARM_url), "r", false, $context);
	$roster = addslashes(stream_get_contents($side));

	$roster = str_replace([chr(10), chr(13)], '', $roster);  // fix newline issues
	$roster = stristr($roster, "<table class=\\\"default_dgrd"); // get only table data from page
	$roster = substr($roster, 0, (stripos($roster, "table>")+6));

?> 

	<div id="other_page" style="display:none;"></div>

	<script>
	$(document).ready( function() {
		$("#other_page").html("<?= $roster ?>");
		$("#CHSabbr, #parseTableHTML").hide()  // hide unneeded things
		parseRosterSIDEARM($('#other_page').html());
	});
	</script>

<?php
}
if ($csv) {
	if ($archive) {
		$query = "UPDATE players SET team='".$team_sel."-old' WHERE team='".$team_sel."'";
		mysql_query($query) or die("<b>YOU DID SOMETHING WRONG</b>.\n<br>Query: " . $query . "<br>\nError: (" . mysql_errno() . ") " . mysql_error());
		echo("Archived players from the old " . $team_sel . " roster.\n");	
	}

	$lines = explode("\r\n",$csv);
	foreach($lines as $line) {
		$values = explode('|',$line);
		$query = "INSERT INTO players (num,first,last,pos,height,weight,year,hometown,stype,s1,s2,s3,s4,s5,s6,s7,s8,team) VALUES ";
		$query .= "('$values[0]','$values[1]','$values[2]','$values[3]','$values[4]','$values[5]','$values[6]','$values[7]','$values[8]','$values[9]','$values[10]','$values[11]','$values[12]','$values[13]','$values[14]','$values[15]','$values[16]','$team_sel')";
		mysql_query($query) or die("<b>YOU DID SOMETHING WRONG</b>.\n<br>Query: " . $query . "<br>\nError: (" . mysql_errno() . ") " . mysql_error());
		echo("Added " . $values[1] . " " . $values[2] . " to the team roster for " . $team_sel);	
	}
	
	$result = mysql_query("SELECT * FROM teams WHERE player_abbrev='$team_sel'");
	$chn_puller = mysql_fetch_assoc($result);

?>
	<br><a href="statsloader.php?tid=<?= $chn_puller['chn_id'] ?>">Update Stats</a>
<?php
  include("peditor.php");
} else { 
?>

<form id="CHSabbr" action="addTeamCsv.php">
	<label>Enter CollegeHockeyStats abbreviation: 
		<input type="text" name="pull_url" size="10" maxlength="4">
		<input type="submit" name="pull">
	</label>
</form>

<div id="boxSIDEARM">
	<form id=parseSIDEARM" action="addTeamCsv.php">
		<label>Parse SIDEARM:
			<div id="urlSIDEARM" style="display: inline;">
				<input type="text" name="sidearm_url" size="80">
				<input type="submit" name="parseSIDEARMButton">
			</div>
			<div id="rosterSIDEARM" style="visibility: auto;"></div>
		</label>
	</form>
</div>

<div id="parseTableHTML">
	<div id="rosterTable" style="visibility: auto;"></div>
	<div id="statsTable" style="visibility: auto;"></div>
	<label>Parse HTML Table:
		<button id="showTableEntry" onclick="$('#tableEntry').toggle()">Toggle Table Entry Box</button><br>
		<div id="tableEntry" style="display:none;">
			<textarea id="tableHTML" rows="10" cols="100"></textarea>
			<button id="parseButton" onclick="parse_table_HTML($('#tableHTML').val());">Parse Table</button>
		</div>
	</label>
</div>

<br/>
<form method="POST" onsubmit="return validateFinalSubmission();">
	<label>Team Name: <input id="team_box" type="text" name="team_sel" size="10" /> (Form: organization-team)</label>
	<p>Entries must be in the form: num|first|last|pos|height|weight|year|hometown|stype|s1|s2|s3|s4|s5|s6|s7|s8|draft<br>
	Missing information must be delimited (e.g., no weight -> ...height||year...)<br>
	Missing information or stats at the end of the line can be ignored.</p>
	<textarea id="csv_textarea" name="csv" rows="30" cols="100"></textarea>
	<br>
	<input type="checkbox" name="archive" value="1" checked/>Archive Current Players?
	<br>
	<input type="submit" name="Submit">
</form>

<?php
}
?>
