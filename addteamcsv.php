<title>Team Roster Adder (via CSV)</title>
<?
include ("init.php");
include ("include.php");
ini_set("auto_detect_line_endings", true);
$team_sel = $_POST["team_sel"];
$csv = $_POST["csv"];
$chs_prefix = $_GET["pull_url"];

if($_GET['pull_url']) {
  $chs = fopen("http://www.collegehockeystats.net/1314/rosters/" . $chs_prefix, "r");
  $contents = addslashes(stream_get_contents($chs));
  $contents = str_replace(chr(10), '', $contents);
  $contents = str_replace(chr(13), '', $contents);
  //$contents = preg_replace('/\p{C}+/u', '', $contents);
  $contents = stristr($contents, "<TABLE");
  $contents = substr($contents, 0, (strrpos($contents, "</TABLE>")+8));
  ?> 
  
  <script src="js/lib/jquery-1.5.1.min.js" type="text/javascript"></script>
  <script src="./parse_roster.js"></script>

  <div id="other_page" style=""></div>

  <script>
    var content_html = "<?= $contents ?>";
  </script>
  <script>
    $("#other_page").html(content_html);
    //alert($("#other_page .rostable").first().html());
    $("#other_page").html("<table>"+$("#other_page .rostable").first().html()+"</table>");
    //$("#tableHTML").val("wat");//$("#other_page").html());
    //parse_table_HTML($("#other_page").html());
  </script>



  <?
}

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
<form action="addteamcsv.php">
  <label>Pull from CollegeHockeyStats URL: (not working yet)
    <input type="text" name="pull_url" size="100" />
    <input type="submit" name="pull" onclick=""></button>
  </label>
</form>
<br/>
<label>Parse HTML Table:<br/>
  <div id="rosterTable" style="visibility: auto;"></div>
  <div id="tableEntry">
    <textarea id="tableHTML" rows="10" cols="100"></textarea>
    <button id="parseButton" onclick="parse_table_HTML($('#tableHTML').val());">Parse Roster</button>
    <button id="CHSbutton" onclick="parse_table_HTML($('#other_page').html());">Parse CHS</button>
  </div>
  <button id="showTableEntry" onclick="$('#tableEntry').toggle()">Toggle Table Entry Form</button>
</label>

<br/>
<form action="addteamcsv.php" method="POST" onsubmit="return validateFinalSubmission();">
  <label>Team Name:
    <input id="team_box" type="text" name="team_sel" size="10" /> (Form: organization-team)
  </label>
  <p>Entries should be in the form: num|first|last|pos|height|weight|year|hometown|stype|s1|s2|s3|s4|s5|s6|s7|s8<br/>
  Missing information must be delimited (e.g., no weight -> ...height||year...)<br/>
  Missing information or stats at the end of the line can be ignored.</p>
  <textarea id="csv_textarea" name="csv" rows="30" cols="100"></textarea>
  <input type="submit" name="Submit" />
</form>
<script src="./js/lib/jquery-1.8.3.js" type="text/javascript"></script>
<script src="./parse_roster.js"></script>
<script src="./state_province.js"></script>
<? } ?>

