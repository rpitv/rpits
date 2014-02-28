<style type="text/css">
	.title img {
	 margin-left:-354px;
	 margin-top:-797px;
	 margin-right:-310px;
	 margin-bottom:-35px;
	}
	.title {
		overflow:hidden;
		width:1220px;
		height:250px;
	}
		 
</style>

<?
include_once("include.php");

$cache = $_GET["cache"];
if ($cache)
{
  $query = "TRUNCATE TABLE cache";
  $result = mysql_query($query) or die("<b>YOU DID SOMETHING WRONG YOU IDIOT</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
}?>
<h3>genall.php</h3>
<form action="genall.php" method="GET">
  Clear Cache?<input type="submit" value="Empty" name="cache">
</form>


<form action="genall.php" method="GET">
Team 1: <select name="team1">
<?

$options = '';

$query = "SELECT * from teams";
$result = dbquery($query);
while($row = mysql_fetch_array($result)){
	$team = fetchTeam($row['player_abbrev']);
	$options .= '<option value="'.$team['player_abbrev']. '">' . $team["sname"] . " " . $team['sport'] . " - " . $team['abbrev'] . "</option>";
}
echo $options;
?>
</select>
Team 2: <select name="team2">
<?
echo $options;
?>
</select>
<input type="submit" name="Submit">
</form>
<?
if ($team1 = $_GET["team1"]){
	$result = dbquery("SELECT * from players WHERE team = '$team1' ORDER BY num");
	echo("<h3>Team 1: " . $team1 . "</h3>\n");
	while($row = mysql_fetch_array($result)){
		echo('<div class="title"><a href="im_render_title.php?player=' . $row["id"] . '&bustCache=true"><img src="im_render_title.php?player=' . $row["id"] . '"></a></div>');
	}
}
if ($team2 = $_GET["team2"]){
	$result = dbquery("SELECT * from players WHERE team = '$team2' ORDER BY num");
	echo("<h3>Team 2: " . $team2 . "</h3>\n");
	while($row = mysql_fetch_array($result)){
		echo('<div class="title"><a href="im_render_title.php?player=' . $row["id"] . '&bustCache=true"><img src="im_render_title.php?player=' . $row["id"] . '"></a></div>');
	}
}
