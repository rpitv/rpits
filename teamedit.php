<title>Team List Editor</title>

<h3>teamedit.php</h3>
<?
include ("include.php");

$edit = $_GET["edit"] ?? '';
$gid = $_GET["id"] ?? '';
?>
<table width="0" border="1" cellspacing="0" cellpadding="2">
	<tr>
		<td>id</td>
		<td>name</td>
		<td>color</td>
		<td>colorr</td>
		<td>colorg</td>
		<td>colorb</td>
		<td>logo</td>
		<td>logo</td>
		<td>logor</td>
		<td>logog</td>
		<td>logob</td>
		<td>start</td>
		<td>end</td>
		<td>womens</td>
		<td>statsid</td>
		<td>edit</td>
	</tr>
<?

$update = $_GET["update"] ?? '';
$name = $_GET["name"] ?? '';
$colorr = $_GET["colorr"] ?? '';
$colorg = $_GET["colorg"] ?? '';
$colorb = $_GET["colorb"] ?? '';
$logo = $_GET["logo"] ?? '';
$logor = $_GET["logor"] ?? '';
$logog = $_GET["logog"] ?? '';
$logob = $_GET["logob"] ?? '';
$start = $_GET["start"] ?? '';
$end = $_GET["end"] ?? '';
$womens = $_GET["womens"] ?? '';
$statsid = $_GET["statsid"] ?? '';

if($update == "Update"){
	$query = "UPDATE `statscard_teams` SET `name` = '$name', `colorr` = '$colorr', `colorg` = '$colorg', `colorb` = '$colorb', `logor` = '$logor', `logog` = '$logog', `logob` = '$logob', `logo` = '$logo', `start` = '$start', `start` = '$start', `end` = '$end', `womens` = '$womens', `statsid` = '$statsid' WHERE `id`='$gid' ;";
	$result = dbQuery($query) or die("<b>YOU DID SOMETHING WRONG YOU IDIOT</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
}
if(($_GET["add"] ?? '') == "Add"){
	$query = "INSERT INTO `statscard_teams` (`name`, `colorr`, `colorg`, `colorb`, `logo`, `logor`, `logog`, `logob`, `start`, `end`, `womens`, `statsid`) VALUES ('$name', '$colorr', '$colorg', '$colorb', '$logo', '$logor', '$logog', '$logob', '$start', '$end', '$womens', '$statsid');";
	$result = dbQuery($query) or die("<b>YOU DID SOMETHING WRONG YOU IDIOT</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
}
$query = "SELECT * from statscard_teams";
$result = dbQuery($query) or die("<b>YOU DID SOMETHING WRONG YOU IDIOT</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
while($row = $result->fetch_array()){
	if($edit == 1 && $gid == $row["id"]){
		echo("	<tr>");
		echo("	<form action=\"" . $_SERVER['PHP_SELF'] . "#" . $row["id"] . "\" method=\"GET\" >\n");
		echo("	<input type=\"hidden\" name=\"id\" value=\"" . $row["id"] . "\">\n");
		echo("		<td><a name=\"" . $row["id"] . "\">". $row["id"] . "</a></td>\n");
		echo("		<td><input type=\"text\" name=\"name\" value=\"" . $row["name"] . "\" /></td>");
		echo("		<td style=\"background:" . rgbhex($row["colorr"],$row["colorg"],$row["colorb"]) . ";\" ></td>");
		echo("		<td><input type=\"text\" size=\"3\" name=\"colorr\" value=\"" . $row["colorr"] . "\" /></td>");
		echo("		<td><input type=\"text\" size=\"3\" name=\"colorg\" value=\"" . $row["colorg"] . "\" /></td>");
		echo("		<td><input type=\"text\" size=\"3\" name=\"colorb\" value=\"" . $row["colorb"] . "\" /></td>");
		echo("		<td style=\"background:" . rgbhex($row["logor"],$row["logog"],$row["logob"]) . ";\" ><img src=\"teamlogos/" . $row["logo"] . "\" /></td>");
		echo("		<td><input type=\"text\" name=\"logo\" value=\"" . $row["logo"] . "\" /></td>");
		echo("		<td><input type=\"text\" size=\"3\" name=\"logor\" value=\"" . $row["logor"] . "\" /></td>");
		echo("		<td><input type=\"text\" size=\"3\" name=\"logog\" value=\"" . $row["logog"] . "\" /></td>");
		echo("		<td><input type=\"text\" size=\"3\" name=\"logob\" value=\"" . $row["logob"] . "\" /></td>");
		echo("		<td><input type=\"text\" size=\"3\" name=\"start\" value=\"" . $row["start"] . "\" /></td>");
		echo("		<td><input type=\"text\" size=\"3\" name=\"end\" value=\"" . $row["end"] . "\" /></td>");
		echo("		<td><input type=\"text\" size=\"1\" name=\"womens\" value=\"" . $row["womens"] . "\" /></td>");
		echo("		<td><input type=\"text\" size=\"1\" name=\"statsid\" value=\"" . $row["statsid"] . "\" /></td>");
		echo("		<td><input type=\"submit\" name=\"update\" value=\"Update\"></td>\n");
		echo("		</form>");
		echo("	</tr>");
	} else {
          if($row["hidden"] != 1)
          {
		echo("	<tr>");
		echo("    <td><a name=\"" . $row["id"] . "\">". $row["id"] . "</a></td>\n");
		echo("    <td>" . $row["name"] . "</td>\n");
		echo("    <td style=\"background:" . rgbhex($row["colorr"],$row["colorg"],$row["colorb"]) . ";\" ></td>");
		echo("    <td>" . $row["colorr"] . "</td>\n");
		echo("    <td>" . $row["colorg"] . "</td>\n");
		echo("    <td>" . $row["colorb"] . "</td>\n");
		echo("    <td style=\"background:" . rgbhex($row["logor"],$row["logog"],$row["logob"]) . ";\" ><img width=30 height=30 src=\"teamlogos/" . $row["logo"] . "\" /></td>");
		echo("	  <td>" . $row["logo"] . "</td>\n");
		echo("    <td>" . $row["logor"] . "</td>\n");
		echo("    <td>" . $row["logog"] . "</td>\n");
		echo("    <td>" . $row["logob"] . "</td>\n");
		echo("    <td>" . $row["start"] . "</td>\n");
		echo("    <td>" . $row["end"] . "</td>\n");
		echo("    <td>" . $row["womens"] . "</td>\n");
		if(intval($row["statsid"]) != 0) {
		  echo("    <td><a href=\"statsloader.php?tid=" . $row["statsid"] . "\">CHN Stats (Men only)</a></td>\n");
		} else if(strlen($row["statsid"])> 1) {
			echo("    <td><a href=\"chsloader.php?url=http://collegehockeystats.net/1213/teamstats/" . $row["statsid"] . "&team=".$row["name"]."\">CHS Stats (No +/-)</a></td>\n");
		} else {
		  echo("<td></td>");
		}
		echo("    <td><a href=\"" . $_SERVER['PHP_SELF'] . "?edit=1&id=" . $row["id"] .  "\">Edit</a></td>\n");
		echo("	</tr>");
          }
	}
	?>
<?
}
?>
<tr>
	<form action="teamedit.php" method="GET" >
	<td>Add</td>
	<td><input type="text" name="name" /></td>
	<td></td>
	<td><input type="text" size="3" name="colorr" /></td>
	<td><input type="text" size="3" name="colorg" /></td>
	<td><input type="text" size="3" name="colorb" /></td>
	<td></td>
	<td><input type="text" name="logo" /></td>
	<td><input type="text" size="3" name="logor" /></td>
	<td><input type="text" size="3" name="logog" /></td>
	<td><input type="text" size="3" name="logob" /></td>
	<td><input type="text" size="3" name="start" /></td>
	<td><input type="text" size="3" name="end" /></td>
	<td><input type="text" size="1" name="womens" /></td>
	<td><input type="text" size="1" name="statsid" /></td>
	<td><input type="submit" name="add" value="Add"></td>
</tr>
