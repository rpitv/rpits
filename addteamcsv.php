<title>Team Roster Adder</title>

<script src="./js/lib/jquery-1.8.3.js" type="text/javascript"></script>
<script src="./parseRoster.js"></script>
<script src="./state_province.js"></script>

<h1>Add Team Roster</h1>

<?
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
	if (date('n')>7) {
		$season = date('y') . (date('y')+1);
	} else {
		$season = (date('y')-1) . date('y');
	}

	$chs = fopen("http://www.collegehockeystats.net/". $season ."/rosters/" . $chs_prefix, "r");
	$contents = addslashes(stream_get_contents($chs));
	$contents = str_replace(chr(10), '', $contents);  // fix newline issues
	$contents = str_replace(chr(13), '', $contents);
	$contents = stristr($contents, "<TABLE"); // get only table data from page
	$contents = substr($contents, 0, (strrpos($contents, "</TABLE>")+8));

	$chs_stats = fopen("http://www.collegehockeystats.net/". $season ."/teamstats/" . $chs_prefix, "r");
	$contents_stats = addslashes(stream_get_contents($chs_stats));
	$contents_stats = str_replace(chr(10), '~', $contents_stats);  // fix newline issues, delimit with '~'
	$contents_stats = str_replace(chr(13), '', $contents_stats);
	$contents_stats = stristr($contents_stats, '<PRE CLASS=\"tiny\">'); // get only stats data from page
	$contents_stats = substr(trim($contents_stats), 20, (strrpos($contents_stats, "</PRE>")-21));
	$contents_stats = explode('~', $contents_stats);

	$result = mysql_query("SELECT * FROM teams WHERE chs_abbrev='$chs_prefix'");
	$team_preset = mysql_fetch_assoc($result);

?> 

	<div id="other_page" style="display:none;"></div>

	<script>
	$(document).ready( function() {
		var content_stat = <? echo(json_encode($contents_stats)); ?>;

		var content_html = "<?= $contents ?>";
		$("#other_page").html(content_html);
		$("#other_page").html("<table>"+$("#other_page .rostable").first().html()+"</table>");
		$("#CHSabbr, #parseTableHTML, #boxSIDEARM").hide()  // hide unneeded things
		parse_table_HTML($('#other_page').html(), content_stat);
		$("#team_box").val("<?= $team_preset['player_abbrev'] ?>");
	});
	</script>

<?
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
	//$side = fopen("http://www.rpiathletics.com/roster.aspx?path=mlax", "r", false, $context);
	$side = fopen(rawurldecode($SIDEARM_url), "r", false, $context);
	$contents = addslashes(stream_get_contents($side));

	$contents = str_replace(chr(10), '', $contents);  // fix newline issues
	$contents = str_replace(chr(13), '', $contents);
	$contents = stristr($contents, "<table class=\\\"default_dgrd"); // get only table data from page
	$contents = substr($contents, 0, (stripos($contents, "table>")+6));

	// SIDEARM stat scraping is on hold
/*	$base_url = substr($SIDEARM_url, 0, stripos($SIDEARM_url, "roster.aspx"));
	$path = substr($SIDEARM_url, (stripos($SIDEARM_url, "path=")+5));
	if (stripos($path, "&")) { // get rid of other args from path
		$path = substr_replace($path, '', stripos($path, "&"));
	}
	$stat_url = $base_url . "cumestats.aspx?path=" . $path . "&year=" . date('Y'); // breaks on winter sports
	echo($stat_url);*/

/*	$sidearm_stream = fopen($stat_url, "r", false, $context);
	$sidearm_stats = addslashes(stream_get_contents($sidearm_stream));
	$contents_stats = str_replace(chr(10), '~', $contents_stats);  // fix newline issues, delimit with '~'
	$contents_stats = str_replace(chr(13), '', $contents_stats);
	$contents_stats = stristr($contents_stats, '<PRE CLASS=\"tiny\">'); // get only stats data from page
	$contents_stats = substr(trim($contents_stats), 20, (strrpos($contents_stats, "</PRE>")-21));
	$contents_stats = explode('~', $contents_stats);*/

?> 

	<div id="other_page" style="display:none;"></div>

	<script>
	$(document).ready( function() {
		$("#other_page").html("<?= $contents ?>");
		$("#CHSabbr, #parseTableHTML").hide()  // hide unneeded things
		parseRosterSIDEARM($('#other_page').html());
	});
	</script>

<?
}



if ($csv) {
	if ($archive) {
		$query = "UPDATE players SET team='".$team_sel."-old' WHERE team='".$team_sel."'";
		mysql_query($query) or die("<b>YOU DID SOMETHING WRONG</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
		echo("Archived players from the old " . $team_sel . " roster.\n");	
	}

	$lines = explode("\r\n",$csv);
	foreach($lines as $line) {
		$values = explode('|',$line);
		$query = "INSERT INTO players (num,first,last,pos,height,weight,year,hometown,stype,s1,s2,s3,s4,s5,s6,s7,s8,team) VALUES ";
		$query .= "('$values[0]','$values[1]','$values[2]','$values[3]','$values[4]','$values[5]','$values[6]','$values[7]','$values[8]','$values[9]','$values[10]','$values[11]','$values[12]','$values[13]','$values[14]','$values[15]','$values[16]','$team_sel')";
		mysql_query($query) or die("<b>YOU DID SOMETHING WRONG</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
		echo("Added " . $values[1] . " " . $values[2] . " to the team roster for " . $team_sel);	
	}
	
	$result = mysql_query("SELECT * FROM teams WHERE player_abbrev='$team_sel'");
	$chn_puller = mysql_fetch_assoc($result);

?>
	<br/><a href="statsloader.php?tid=<?= $chn_puller['chn_id'] ?>">Update Stats</a>
<?
  include("peditor.php");
} else { 
?>

<form id="CHSabbr" action="addteamcsv.php">
	<label>Enter CollegeHockeyStats abbreviation: 
		<input type="text" name="pull_url" size="10" maxlength="4" />
		<input type="submit" name="pull" onclick=""></button>
	</label>
</form>
<!--<button id="CHSbutton" onclick="parse_table_HTML($('#other_page').html());">Parse CHS</button>-->

<div id="boxSIDEARM">
	<form id=parseSIDEARM" action="addteamcsv.php">
		<label>Parse SIDEARM:
			<div id="urlSIDEARM" style="display: inline;">
				<input type="text" name="sidearm_url" size="80" />
				<input type="submit" name="parseSIDEARMButton" onclick=""></button>
			</div>
			<div id="rosterSIDEARM" style="visibility: auto;"></div>
		</label>
	</form>
</div>

<div id="parseTableHTML">
	<label>Parse HTML Table:
		<button id="showTableEntry" onclick="$('#tableEntry').toggle()">Toggle Table Entry Box</button><br/>
		<div id="rosterTable" style="visibility: auto;"></div>
		<div id="tableEntry" style="display:none;">
			<textarea id="tableHTML" rows="10" cols="100"></textarea>
			<button id="parseButton" onclick="parse_table_HTML($('#tableHTML').val());">Parse Table</button>
		</div>
	</label>
</div>

<br/>
<form action="addteamcsv.php" method="POST" onsubmit="return validateFinalSubmission();">
	<label>Team Name: <input id="team_box" type="text" name="team_sel" size="10" /> (Form: organization-team)</label>
	<p>Entries should be in the form: num|first|last|pos|height|weight|year|hometown|stype|s1|s2|s3|s4|s5|s6|s7|s8<br/>
	Missing information must be delimited (e.g., no weight -> ...height||year...)<br/>
	Missing information or stats at the end of the line can be ignored.</p>
	<textarea id="csv_textarea" name="csv" rows="30" cols="100"></textarea>
	<br/>
	<input type="checkbox" name="archive" value="1" checked/>Archive Current Players?
	<br/>
	<input type="submit" name="Submit" />
</form>

<? } ?>
