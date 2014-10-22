<title>Player Editor</title>
<?php
include ("init.php");

$team_sel = $_POST["team_sel"];

$id = $_POST["id"];
$num = $_POST["num"];
$first = $_POST["first"];
$last = $_POST["last"];
$pos = $_POST["pos"];
$height = $_POST["height"];
$weight = $_POST["weight"];
$year = $_POST["year"];
$hometown = $_POST["hometown"];
$stype = $_POST["stype"];
$s1 = $_POST["s1"];
$s2 = $_POST["s2"];
$s3 = $_POST["s3"];
$s4 = $_POST["s4"];
$s5 = $_POST["s5"];
$s6 = $_POST["s6"];
$s7 = $_POST["s7"];
$s8 = $_POST["s8"];
$team = $_POST["team"];

if ($id) {
	$query = "UPDATE players SET id='$id', num='$num', first='$first', last='$last', pos='$pos', height='$height', weight='$weight', year='$year', hometown='$hometown', stype='$stype', ";
	$query .= "s1='$s1', s2='$s2', s3='$s3', s4='$s4', s5='$s5', s6='$s6', s7='$s7', s8='$s8', team='$team' WHERE id='$id'" ;
	mysql_query($query) or die("<b>Something went wrong</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
	echo("<p>" . $first . " " . $last . " was updated.");
} else if($num || $first) {
	$query = "INSERT INTO players (num,first,last,pos,height,weight,year,hometown,stype,s1,s2,s3,s4,s5,s6,s7,s8,team) ";
	$query .= "VALUES ('$num','$first','$last','$pos','$height','$weight','$year','$hometown','$stype','$s1','$s2','$s3','$s4','$s5','$s6','$s7','$s8','$team')";
	mysql_query($query) or die("<b>>Something went wrong</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
	echo("<p>" . $first . " " . $last . " was inserted.");
}

if ($team_sel) {
?>
<table border="1">
	<tr><form action="peditor.php" method="post">
	<td></td>
	<td><input type="text" size="2" name="num"></td>
	<td><input type="text" size="4" name="first"></td>
	<td><input type="text" size="10" name="last"></td>
	<td><input type="text" size="1" name="pos"></td>
	<td><input type="text" size="2" name="height"></td>
	<td><input type="text" size="2" name="weight"></td>
	<td><input type="text" size="2" name="year"></td>
	<td><input type="text" size="18" name="hometown"></td>
	<td><input type="text" size="1" name="stype"></td>
	<td><input type="text" size="1" name="s1"></td>
	<td><input type="text" size="1" name="s2"></td>
	<td><input type="text" size="1" name="s3"></td>
	<td><input type="text" size="1" name="s4"></td>
	<td><input type="text" size="1" name="s5"></td>
	<td><input type="text" size="1" name="s6"></td>
	<td><input type="text" size="1" name="s7"></td>
	<td><textarea name=\"s8\" rows=1 cols=20></textarea></td>
	<td><input type="text" size="2" name="team"></td>
	<td><input type="hidden" name="team_sel" value="<? echo($team_sel); ?>"><input type="submit" name="submit" value="Save"></td>
	<td><a href="im_render_title.php?player=1">Link</a></td></form></tr>
<?php
	if ($team_sel) {
		$query = "SELECT * FROM players WHERE team='$team_sel' ORDER BY num ASC";
	}
	$result = mysql_query($query) or die("<b>Something went wrong</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
	while ($row = mysql_fetch_array($result)) {
		echo("<tr><form action=\"peditor.php\" method=\"post\"><td><input type=\"hidden\" name=\"id\" value=\"".$row["id"]."\">" . $row["id"]);
		echo("</td><td><input type=\"text\" size=\"2\" name=\"num\" value=\"" . $row["num"] . "\">");
		echo("</td><td><input type=\"text\" size=\"4\" name=\"first\" value=\"" . $row["first"] . "\">");
		echo("</td><td><input type=\"text\" size=\"10\" name=\"last\" value=\"" . $row["last"] . "\">");
		echo("</td><td><input type=\"text\" size=\"1\" name=\"pos\" value=\"" . $row["pos"] . "\">");
		echo("</td><td><input type=\"text\" size=\"2\" name=\"height\" value=\"" . $row["height"] . "\">");
		echo("</td><td><input type=\"text\" size=\"2\" name=\"weight\" value=\"" . $row["weight"] . "\">");
		echo("</td><td><input type=\"text\" size=\"2\" name=\"year\" value=\"" . $row["year"] . "\">");
		echo("</td><td><input type=\"text\" size=\"18\" name=\"hometown\" value=\"" . $row["hometown"] . "\">");
		echo("</td><td><input type=\"text\" size=\"1\" name=\"stype\" value=\"" . $row["stype"] . "\">");
		echo("</td><td><input type=\"text\" size=\"1\" name=\"s1\" value=\"" . $row["s1"] . "\">");
		echo("</td><td><input type=\"text\" size=\"1\" name=\"s2\" value=\"" . $row["s2"] . "\">");
		echo("</td><td><input type=\"text\" size=\"1\" name=\"s3\" value=\"" . $row["s3"] . "\">");
		echo("</td><td><input type=\"text\" size=\"1\" name=\"s4\" value=\"" . $row["s4"] . "\">");
		echo("</td><td><input type=\"text\" size=\"1\" name=\"s5\" value=\"" . $row["s5"] . "\">");
		echo("</td><td><input type=\"text\" size=\"1\" name=\"s6\" value=\"" . $row["s6"] . "\">");
		echo("</td><td><input type=\"text\" size=\"1\" name=\"s7\" value=\"" . $row["s7"] . "\">");
		echo("</td><td><textarea name=\"s8\" rows=1 cols=20>" . $row["s8"] . "</textarea>");
		echo("</td><td><input type=\"text\" size=\"2\" name=\"team\" value=\"" . $row["team"] . "\"></td>");
		echo("<td><input type=\"hidden\" name=\"team_sel\" value=\"".$team_sel."\"><input type=\"submit\" name=\"submit\" value=\"Save\"></td>");
		echo("<td><a href=\"im_render_title.php?player=" . $row["id"] . "\">Link</a></td></form>\n");
		echo("</tr>");
	}
	echo("</table>");
	} else {
?>
		<h2>Select a team</h2>
		<form action="peditor.php" method="post">
<?php
			$query = "SELECT * FROM statscard_teams";
			$result = mysql_query($query) or die("<b>Something went wrong</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
			while ($row = mysql_fetch_array($result)) {
				echo("<div style=\"float:left;min-width:100px;height:100px;\"><img width=\"30\" src=\"teamlogos/" . $row["logo"] . "\"><br><input type=\"submit\" name=\"team_sel\" value=\"" . $row["name"] . "\"></div>");
			}
?>
		</form>
<?php
	}
?>
