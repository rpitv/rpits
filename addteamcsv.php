<title>Team Roster Adder (via CSV)</title>
<?
include ("init.php");
mysql_select_db("rpihockey");
$team_sel = $_POST["team_sel"];
$csv = $_POST["csv"];

if($csv)
{
	$lines = explode("\r\n",$csv);
	foreach($lines as $line)
	{
		$values = explode('|',$line);
		$query = "INSERT INTO players (num,first,last,pos,height,weight,year,hometown,team) VALUES ";
		$query .= "('$values[0]','$values[1]','$values[2]','$values[3]','$values[4]','$values[5]','$values[6]','$values[7]','$team_sel')";
		mysql_query($query) or die("<b>YOU DID SOMETHING WRONG YOU IDIOT</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
		echo("Added " . $values[1] . " " . $values[2] . " to the team roster for " . $team_sel);	
	}
	include("peditor.php");
}
else
{ 
?>
<form action="addteamcsv.php" method="POST">
<input type="text" name="team_sel" size="10" />
<textarea name="csv" rows="30" cols="100"></textarea>
<input type="submit" name="Submit" />
</form>
<? } ?>


