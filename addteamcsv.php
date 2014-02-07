<title>Team Roster Adder (via CSV)</title>
<?
include ("init.php");
$team_sel = $_POST["team_sel"];
$csv = $_POST["csv"];

if($csv)
{
	$lines = explode("\r\n",$csv);
	foreach($lines as $line)
	{
		$values = explode('|',$line);
		$query = "INSERT INTO players (num,first,last,pos,height,weight,year,hometown,stype,s1,s2,s3,s4,s5,s6,s7,s8,team) VALUES ";
		$query .= "('$values[0]','$values[1]','$values[2]','$values[3]','$values[4]','$values[5]','$values[6]','$values[7]','$values[8]','$values[9]','$values[10]','$values[11]','$values[12]','$values[13]','$values[14]','$values[15]','$values[16]','$team_sel')";
		mysql_query($query) or die("<b>YOU DID SOMETHING WRONG YOU IDIOT</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
		echo("Added " . $values[1] . " " . $values[2] . " to the team roster for " . $team_sel);	
	}
	include("peditor.php");
}
else
{ 
?>

<h1>Add Team Roster via CSV</h1>
<form action="addteamcsv.php" method="POST">
  <label>Team Name:
    <input type="text" name="team_sel" size="10" /> (Form: organization-team)
  </label>
  <br/>
  <label>Pull from CollegeHockeyStats URL: (not working yet)
    <input type="text" name="pull_url" size="100" />
    <button name="pull" onclick="alert(pull_url.value)">Parse Roster</button>
  </label>
  <br/>
  <p>Entries should be in the form: num|first|last|pos|height|weight|year|hometown|stype|s1|s2|s3|s4|s5|s6|s7|s8<br/>
  Missing information must be delimited (e.g., no weight -> ...height||year...)<br/>
  Missing information or stats at the end of the line can be ignored.</p>
  <textarea name="csv" rows="30" cols="100"></textarea>
  <input type="submit" name="Submit" />
</form>
<? } ?>

