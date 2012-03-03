<?php
mysql_connect("localhost","root","");
mysql_select_db("rpihockey");

$id = $_GET["id"];
$type = $_GET["type"];

if ($type == "billboard")
{
  $query =  "SELECT * from billboards WHERE `id` = '$id'";
  $result = mysql_query($query) or die("<b>YOU DID SOMETHING WRONG YOU IDIOT</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
  $row = mysql_fetch_array($result);
  
  $png = file_get_contents("http://localhost/hockey/billboards/" . $row["file_name"]);
  ?>
  <svg width="1920" height="1080" viewBox="0 0 1920 1080" version="1.1">
  <image x="360" y="165"  width="1200" height="750" xlink:href="data:image/png;base64,<?= base64_encode($png) ?>" />
  </svg>
  <?
  return;
}
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

<? /*
<path d="M 0 0 L 1920 0 L 1920 1080 L 0 1080 Z" stroke="#000000" stroke-width="5" fill="rgba(0,0,0,0)"/>
<path d="M 100 60 L 1820 60 L 1820 1020 L 100 1020 Z" stroke="red" stroke-width="5" fill="rgba(0,0,0,0)"/>
<path d="M 200 120 L 1720 120 L 1720 960 L 200 960 Z" stroke="green" stroke-width="5" fill="rgba(0,0,0,0)"/>
<path d="M 240 0 L 240 1080" stroke="#000000" stroke-width="5"/>
<path d="M 320 0 L 320 1080" stroke="red" stroke-width="5"/>
<path d="M 400 120 L 400 960" stroke="green" stroke-width="5"/>
<path d="M 1520 120 L 1520 960" stroke="green" stroke-width="5"/>
<path d="M 1600 60 L 1600 1020" stroke="red" stroke-width="5"/>
<path d="M 1680 0 L 1680 1080" stroke="black" stroke-width="5" /> */?>

<image x="360" y="<?= $valign ?>"  width="1200" height="<?= $height ?>" xlink:href="data:image/png;base64,<?= base64_encode($png) ?>" />

</svg>