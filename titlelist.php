<title>General Title Editor - List</title>

<h3>titlelist.php - <a href="titlelist.php">Refresh</a></h3>

<style type="text/css">
<!--
.titles {
	float: left;
}
-->
</style>
<?
mysql_connect("localhost","root","");
mysql_select_db("rpihockey");

$query = "SELECT * from general ORDER BY height ASC";
$result = mysql_query($query) or die("<b>YOU DID SOMETHING WRONG YOU IDIOT</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
while($row = mysql_fetch_array($result)){
	echo("<div class=\"titles\">");
	echo("<a target=\"edit\" href=\"titleedit.php?id=". $row["id"] . "\"><img width=200 src=\"gentitle.php?id=" . $row["id"] . "\" \\></a>");
	echo("</div>");
}
?>
<div class="titles"><h1><a target="edit" href="titleedit.php?new=new">Add New</a></h1></div>
