<html>
<head>

<?
mysql_connect("mysql36.ixwebhosting.com", "cuttlef_rockband", "lucky2");
mysql_select_db("cuttlef_rockband");


$id = $_GET["id"];
$name = $_GET["name"];
$artist = $_GET["artist"];
$type = $_GET["type"];
$guitar = $_GET["guitar"];
$bass = $_GET["bass"];
$drums = $_GET["drums"];
$vocals = $_GET["vocals"];
$band = $_GET["band"];
$wtmor = $_GET["wtmor"];
$wtmos = $_GET["wtmos"];
$rpir = $_GET["rpir"];
$rpis = $_GET["rpis"];
?>
<title>Add Rock Band Song</title>
</head>

<body>
<?
if (isset($id))
{
$query = "INSERT INTO `songs` (`id`,`name`,`artist`,`type`,`guitar`,`bass`,`drums`,`vocals`,`band`,`wtmos`,`wtmor`,`rpis`,`rpir`) VALUES ('$id','$name','$artist','$type','$guitar','$bass','$drums','$vocals','$band','$wtmos','$wtmor','$rpis','$rpir');";

$result = mysql_query($query) or die("<b>YOU DID SOMETHING WRONG YOU IDIOT</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
?>
<h2>Song added!</h2>

<? } ?>
<table width="0" border="1" cellspacing="0" cellpadding="0">
  <tr>
    <td>id</td>
    <td>name</td>
    <td>artist</td>
    <td>type</td>
    <td>guitar</td>
    <td>bass</td>
    <td>drums</td>
    <td>vocals</td>
    <td>band</td>
    <td>wtmos</td>
    <td>wtmor</td>
    <td>rpis</td>
    <td>rpir</td>
    <td>&nbsp;</td>
  </tr>
  <form action="<? echo($PHP_SELF); ?>" method="get">
  <tr>
    <td><input type="text" name="id" size="2"></td>
    <td><input type="text" name="name" size="15"></td>
    <td><input type="text" name="artist" size="15"></td>
    <td><input type="text" name="type" size="2"></td>
    <td><input type="text" name="guitar" size="2"></td>
    <td><input type="text" name="bass" size="2"></td>
    <td><input type="text" name="drums" size="2"></td>
    <td><input type="text" name="vocals" size="2"></td>
    <td><input type="text" name="band" size="2"></td>
    <td><input type="text" name="wtmos" size="10"></td>
    <td><input type="text" name="wtmor" size="10"></td>
    <td><input type="text" name="rpis" size="10"></td>
    <td><input type="text" name="rpir" size="10"></td>
    <td><input type="submit" name="add" value="add"></td>
  </tr>
  </form>
<?
$result = mysql_query("SELECT * FROM songs");
while ( $row = mysql_fetch_array($result) ) {
	echo("<tr>");
	echo("<td>" . $row["id"] . "</td>");
	echo("<td>" . $row["name"] . "</td>");
	echo("<td>" . $row["artist"] . "</td>");
	echo("<td>" . $row["type"] . "</td>");
	echo("<td>" . $row["guitar"] . "</td>");
	echo("<td>" . $row["bass"] . "</td>");
	echo("<td>" . $row["drums"] . "</td>");
	echo("<td>" . $row["vocals"] . "</td>");
	echo("<td>" . $row["band"] . "</td>");
	echo("<td>" . $row["wtmos"] . "</td>");
	echo("<td>" . $row["wtmor"] . "</td>");
	echo("<td>" . $row["rpis"] . "</td>");
	echo("<td>" . $row["rpir"] . "</td>");
	echo('<td><a href="' . $_SERVER['PHP_SELF'] . "?delid=" . $row["id"] . '">del</a></td>');
	echo("</tr>");
	}
	?>
  <tr>
  	<td>id</td>
    <td>name</td>
    <td>artist</td>
    <td>type</td>
    <td>guitar</td>
    <td>bass</td>
    <td>drums</td>
    <td>vocals</td>
    <td>band</td>
    <td>wtmos</td>
    <td>wtmor</td>
    <td>rpis</td>
    <td>rpir</td>
    <td>&nbsp;</td>
  </tr>
</table>
