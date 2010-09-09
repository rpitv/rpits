<title>Goalie Editor</title>
<?
mysql_connect("localhost","root","");
mysql_select_db("rpihockey");

$team_sel = $_POST["team_sel"];
if(!$team_sel)
	$team_sel = $_GET["team_sel"];

$id = $_POST["id"];
$num = $_POST["num"];
$first = $_POST["first"];
$last = $_POST["last"];
$pos = $_POST["pos"];
$height = $_POST["height"];
$weight = $_POST["weight"];
$year = $_POST["year"];
$hometown = $_POST["hometown"];
$gp = $_POST["gp"];
$saveper = $_POST["saveper"];
$gaa = $_POST["gaa"];
$win = $_POST["win"];
$loss = $_POST["loss"];
$tie = $_POST["tie"];
$team = $_POST["team"];
$copy = $_POST["copy"];

if($id)
{
	$query = "UPDATE goalies SET id='$id', num='$num', first='$first', last='$last', pos='$pos', height='$height', weight='$weight', year='$year', hometown='$hometown', gp='$gp', saveper='$saveper', gaa='$gaa', win='$win', loss='$loss', tie='$tie', team='$team' WHERE id='$id'" ;
	mysql_query($query) or die("<b>YOU DID SOMETHING WRONG YOU IDIOT</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
	echo("<p>" . $first . " " . $last . " was updated.");
	} else if($num) {
	$query = "INSERT INTO goalies (num,first,last,pos,height,weight,year,hometown,gp,saveper,gaa,win,loss,tie,team) VALUES ('$num','$first','$last','$pos','$height','$weight','$year','$hometown','$gp','$saveper','$gaa','$win','$loss','$tie','$team')";
	mysql_query($query) or die("<b>YOU DID SOMETHING WRONG YOU IDIOT</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
	echo("<p>" . $first . " " . $last . " was inserted.");
}


if($team_sel){
	?>
	<table border="1">
	<form action="geditor.php" method="post"><tr>
	<td></td>
	<td><input type="text" size="2" name="num"></td>
	<td><input type="text" size="4" name="first"></td>
	<td><input type="text" size="10" name="last"></td>
	<td><input type="text" size="1" name="pos"></td>
	<td><input type="text" size="2" name="height"></td>
	<td><input type="text" size="2" name="weight"></td>
	<td><input type="text" size="2" name="year"></td>
	<td><input type="text" size="18" name="hometown"></td>
	<td><input type="text" size="1" name="gp"></td>
	<td><input type="text" size="1" name="saveper"></td>
	<td><input type="text" size="1" name="gaa"></td>
	<td><input type="text" size="1" name="win"></td>
	<td><input type="text" size="1" name="loss"></td>
	<td><input type="text" size="1" name="tie"></td>
	<td><input type="text" size="2" name="team"></td>
	<td><input type="hidden" name="team_sel" value="<? echo($team_sel); ?>"><input type="submit" name="submit" value="Save"></td>
	<td><a href="http://localhost/hockey/2009lower3rd.php?id=1">Link</a></td></tr></form>

	<?
	if($team_sel)
	{
		$query = "SELECT * FROM goalies WHERE team='$team_sel' AND pos='G' ORDER BY num";
	}
	$result = mysql_query($query) or die("<b>YOU DID SOMETHING WRONG YOU IDIOT</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
	while($row = mysql_fetch_array($result)){
		echo("<form action=\"geditor.php\" method=\"post\"><tr><td><input type=\"hidden\" name=\"id\" value=\"".$row["id"]."\">" . $row["id"]);
		echo("</td><td><input type=\"text\" size=\"2\" name=\"num\" value=\"" . $row["num"] . "\">");
		echo("</td><td><input type=\"text\" size=\"4\" name=\"first\" value=\"" . $row["first"] . "\">");
		echo("</td><td><input type=\"text\" size=\"10\" name=\"last\" value=\"" . $row["last"] . "\">");
		echo("</td><td><input type=\"text\" size=\"1\" name=\"pos\" value=\"" . $row["pos"] . "\">");
		echo("</td><td><input type=\"text\" size=\"2\" name=\"height\" value=\"" . $row["height"] . "\">");
		echo("</td><td><input type=\"text\" size=\"2\" name=\"weight\" value=\"" . $row["weight"] . "\">");
		echo("</td><td><input type=\"text\" size=\"2\" name=\"year\" value=\"" . $row["year"] . "\">");
		echo("</td><td><input type=\"text\" size=\"18\" name=\"hometown\" value=\"" . $row["hometown"] . "\">");
		echo("</td><td><input type=\"text\" size=\"1\" name=\"gp\" value=\"" . $row["gp"] . "\">");
		echo("</td><td><input type=\"text\" size=\"1\" name=\"saveper\" value=\"" . $row["saveper"] . "\">");
		echo("</td><td><input type=\"text\" size=\"1\" name=\"gaa\" value=\"" . $row["gaa"] . "\">");
		echo("</td><td><input type=\"text\" size=\"1\" name=\"win\" value=\"" . $row["win"] . "\">");
		echo("</td><td><input type=\"text\" size=\"1\" name=\"loss\" value=\"" . $row["loss"] . "\">");
		echo("</td><td><input type=\"text\" size=\"1\" name=\"tie\" value=\"" . $row["tie"] . "\">");
		echo("</td><td><input type=\"text\" size=\"2\" name=\"team\" value=\"" . $row["team"] . "\"></td>");
		echo("<td><input type=\"hidden\" name=\"team_sel\" value=\"".$team_sel."\"><input type=\"submit\" name=\"submit\" value=\"Save\"></td>");
		echo("<td><a href=\"http://localhost/hockey/statscard.php?id=" . $row["id"] . "\">Link</a></td></tr></form>\n");

	}
}
else
{
?>
<h2>Select a team</h2>
<form action="geditor.php" method="post">
<table border="1">

<?
$query = "SELECT * FROM teams";
$result = mysql_query($query) or die("<b>YOU DID SOMETHING WRONG YOU IDIOT</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
while($row = mysql_fetch_array($result)){
	echo("<tr><td><img width=\"30\" src=\"teamlogos/" . $row["name"] . ".png\">&nbsp;<input type=\"submit\" name=\"team_sel\" value=\"" . $row["name"] . "\"></td></tr>");
}
?>
</table></form>
<?
}
?>
