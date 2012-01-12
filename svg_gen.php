<?php
mysql_connect("localhost","root","");
mysql_select_db("rpihockey");

$id = $_GET["id"];
$type = $_GET["type"];

if ($type == "general")
{

  $query =  "SELECT * from general WHERE `id` = '$id'";
  $result = mysql_query($query) or die("<b>YOU DID SOMETHING WRONG YOU IDIOT</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
  $row = mysql_fetch_array($result);

  $height = $row["height"];
  $height *= 1.875;
  $valign = 90+(900-$height);

  $png = file_get_contents("http://localhost/hockey/gentitle.php?id=$id");
}
else
{
  $png = file_get_contents("http://localhost/hockey/statscard.php?id=$id");
  $height = 160*1.875;
  $valign = 150+(900-$height);
}

?>
<svg width="1920" height="1080" viewBox="0 0 1920 1080" version="1.1">

<? /*<path d="M 0 0 L 1920 0 L 1920 1080 L 0 1080 Z" stroke="#000000" stroke-width="5" fill="rgba(0,0,0,0)"/>
<path d="M 96 54 L 1824 54 L 1824 1026 L 96 1026 Z" stroke="#000000" stroke-width="5" fill="rgba(0,0,0,0)"/>
<path d="M 240 0 L 240 1080" stroke="#000000" stroke-width="5"/>
<path d="M 1680 0 L 1680 1080" stroke="#000000" stroke-width="5"/> */?>

<image x="360" y="<?= $valign ?>"  width="1200" height="<?= $height ?>" xlink:href="data:image/png;base64,<?= base64_encode($png) ?>" />

</svg>