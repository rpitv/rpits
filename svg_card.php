<?php

mysql_connect("localhost","root","");
mysql_select_db("rpihockey");

$id = $_GET["id"];

$query = "SELECT * FROM players WHERE id = '$id'";
$result = mysql_query($query) or die("<b>YOU DID SOMETHING WRONG YOU IDIOT</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
$row = mysql_fetch_array($result);

$team = $row["team"];
$query = "SELECT * from teams WHERE `name` = '$team'";
$result = mysql_query($query) or die("<b>YOU DID SOMETHING WRONG YOU IDIOT</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
$teamrow = mysql_fetch_array($result);

//print_r($row);
//print_r($teamrow);

$stype = $row["stype"];
if($stype != "txt")
{
  $query = "SELECT * FROM stattype WHERE `type`  = '$stype'";
  $result = mysql_query($query) or die("<b>YOU DID SOMETHING WRONG YOU IDIOT</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
  $slabel = mysql_fetch_array($result);

  $lsize = 17;
  $ssize = 28;

  $owidth = 0;
  $warray = Array();

  for($i=2;$slabel[$i];$i++)
  {
    if($slabel[$i] == "+/-")
    {
      if($row[$i+8] > 0)
	$row[$i+8] = "+" . $row[$i+8];
    }
    $lbox = imagettfbbox($lsize, 0, "fonts/GothamNarrow-Bold.otf", $slabel[$i]);
    $sbox = imagettfbbox($ssize, 0, "fonts/GothamNarrow-Bold.otf", $row[$i+8]);
    $owidth += max($lbox[2],$sbox[2]);
    $warray[] = max($lbox[2],$sbox[2]);
  }
  $spacer = (400-$owidth)/(count($warray)-1);
  //print_r($warray);
  //echo("Owidth: $owidth<br>");
  //echo("Spacer: $spacer<br>");

  $hoffset = 170;

  /* Optional - specify a maximum spacer and center stats
   * Stats can look more than a bit awkward with a huge spacer value
  if($spacer > 30)
  {
    $spacer = 30;
    $hoffset += (400 - $owidth - $spacer*(count($warray)-1))/2;
  }
*/

}

$name_size = 27;
$name_voffset = 0;
$box = imagettfbbox($name_size, 0, "fonts/GothamNarrow-Bold.otf", $row["first"] ." ".$row["last"]);
while($box[2]>255)
{
  $name_size--;
  $box = imagettfbbox($name_size, 0, "fonts/GothamNarrow-Bold.otf", $row["first"] ." ".$row["last"]);
  $name_voffset += .5;
}
$name_size *= 4/3;

?>
<<?='?';?>xml version="1.0" standalone="no"<?='?';?>>
<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">
<svg width="640" height="120" viewBox="0 0 640 120" version="1.1">
<?
$font = "fonts/GothamNarrow-Bold.otf";
$fbinary = fread(fopen($font, "r"),filesize($font));
/*
  <style type="text/css">
   <![CDATA[
     @font-face {
       font-family: 'Font';
       font-weight: normal;
       font-style: normal;
       src: url("data:font/opentype;base64,<?= base64_encode($fbinary) ?>");
     }
   ]]>
  </style>*/ ?>
  <defs>
    <? //include ("fonts/gothamnarrowbold.svg"); ?>
    <linearGradient id="gradient1" x1="50%" y1="0%" x2="50%" y2="100%">
      <stop stop-color="rgb(0,0,0)" stop-opacity="0" offset="0%"/>
      <stop stop-color="rgb(0,0,0)" stop-opacity="0" offset="47%"/>
      <stop stop-color="rgb(0,0,0)" stop-opacity="1" offset="98%"/>
    </linearGradient>
    <linearGradient id="gradient2" x1="50%" y1="0%" x2="50%" y2="100%">
      <stop stop-color="#000000" stop-opacity="1" offset="0%"/>
      <stop stop-color="#000000" stop-opacity="0" offset="47%"/>
      <stop stop-color="#000000" stop-opacity="0" offset="97%"/>
    </linearGradient>
    <linearGradient id="gradient3" x1="50%" y1="0%" x2="50%" y2="100%">
      <stop stop-color="#ffffff" stop-opacity="0" offset="0%"/>
      <stop stop-color="#ffffff" stop-opacity="1" offset="99%"/>
    </linearGradient>
    <linearGradient id="gradient4" x1="50%" y1="0%" x2="50%" y2="100%">
      <stop stop-color="#000000" stop-opacity="0.8980392156862745" offset="0%"/>
      <stop stop-color="#000000" stop-opacity="0.6980392156862745" offset="98%"/>
    </linearGradient>
  </defs>
  <path d="M 50 40 L 590 40 L 590 120 L 50 120 L 50 40 Z" fill="url(#gradient4)"/>
  <? $tcolor = "rgb(".$teamrow["colorr"].",".$teamrow["colorg"].",".$teamrow["colorb"].")"; ?>
  <? $lcolor = "rgb(".$teamrow["logor"].",".$teamrow["logog"].",".$teamrow["logob"].")"; ?>
  <path d="M 30 40 L 50 0 L 410 0 L 610 0 L 590 40 L 30 40 Z" fill="<?= $tcolor ?>"/>
  <path d="M 410 0 L 390 40 L 444 40 L 464 0 L 410 0 Z" fill="#000000"/>
  <path d="M 410 0 L 390 40 L 444 40 L 464 0 L 410 0 Z" fill="#000000" opacity="0.30196078431372547"/>
  <path d="M 500 0 L 480 40 L 530 40 L 550 0 L 500 0 Z" fill="#000000"/>
  <path d="M 550 0 L 530 40 L 590 40 L 610 0 L 550 0 Z" fill="<?= $lcolor ?>"/>
  <path d="M 500 0 L 480 40 L 530 40 L 550 0 L 500 0 Z" fill="#000000" opacity="0.30196078431372547"/>
  <path d="M 30 40 L 40 20 L 600 20 L 590 40 L 30 40 Z" fill="url(#gradient1)" opacity="0.7019607843137255"/>
  <path d="M 40 20 L 50 0 L 610 0 L 600 20 L 40 20 Z" fill="url(#gradient2)" opacity="0.5019607843137255"/>
  <path d="M 40 20 L 50 0 L 610 0 L 600 20 L 40 20 Z" fill="url(#gradient3)" opacity="0.5019607843137255"/>
  <path d="M 30 40 L 50 0 L 610 0 L 590 40 L 30 40 Z" stroke="#000000" stroke-width="1" fill-opacity="0"/>
  <path d="M 550 0 L 530 40 " stroke="#000000" stroke-width="1"/>
  <path d="M 500 0 L 480 40 " stroke="#000000" stroke-width="1"/>
  <path d="M 464 0 L 444 40 " stroke="#000000" stroke-width="1"/>
  <path d="M 410 0 L 390 40 " stroke="#000000" stroke-width="1"/>
  <text x="137" y="<?= 34-$name_voffset ?>" font-family="Gotham Narrow Bold" font-size="<?= $name_size?>" fill="black" ><?= $row["first"] ." ".$row["last"] ?></text>
  <text x="135" y="<?= 32-$name_voffset ?>" font-family="Gotham Narrow Bold" font-size="<?= $name_size?>" fill="white" ><?= $row["first"] ." ".$row["last"] ?></text>
  <text x="428" y="34" style="text-anchor: middle;" font-family="Gotham Narrow Bold" font-size="36" fill="black" ><?= $row["num"] ?></text>
  <text x="426" y="32" style="text-anchor: middle;" font-family="Gotham Narrow Bold" font-size="36" fill="white" ><?= $row["num"] ?></text>
  <text x="472" y="34" style="text-anchor: middle;" font-family="Gotham Narrow Bold" font-size="36" fill="black" ><?= $row["pos"] ?></text>
  <text x="470" y="32" style="text-anchor: middle;" font-family="Gotham Narrow Bold" font-size="36" fill="white" ><?= $row["pos"] ?></text>
  <text x="517" y="34" style="text-anchor: middle;" font-family="Gotham Narrow Bold" font-size="36" fill="black" ><?= $row["year"] ?></text>
  <text x="515" y="32" style="text-anchor: middle;" font-family="Gotham Narrow Bold" font-size="36" fill="white" ><?= $row["year"] ?></text>
  <text x="170" y="56" font-family="Gotham Narrow Bold" font-size="16" fill="white" ><?= "Hometown: ".$row["hometown"] ?><tspan dx="20"><?= "Ht: ".$row["height"] ?></tspan><tspan dx="20"><?= "Wt: ".$row["weight"] ?></tspan></text>
<?
$lheight = 82;
$sheight = 113;

for($i = 0;$i < count($warray);$i++)
{  ?>
  <text x="<?= $hoffset + $warray[$i]/2 ?>" y="82" style="text-anchor: middle;" font-family="Gotham Narrow Bold" font-size="22.6" fill="white"><?= $slabel[$i+2] ?></text>
  <text x="<?= $hoffset + $warray[$i]/2 ?>" y="113" style="text-anchor: middle;" font-family="Gotham Narrow Bold" font-size="37px" fill="white"><?= $row[$i+10] ?></text>
<? 
$hoffset += $warray[$i] + $spacer;
}

?>



<?
$portrait = "teams/" . $row["team"] . "imgs/" . $row["first"] . $row["last"] . ".png";
$pbinary = fread(fopen($portrait, "r"), filesize($portrait));
$logo = 'teamlogos\\' . $teamrow["logo"];
$lbinary = fread(fopen($logo, "r"),filesize($logo));

?>

  <image x="50" y="0" width="100" height="120" xlink:href="data:image/png;base64,<?= base64_encode($pbinary) ?>" />
  <image x="550" y="0" width="40" height="40" xlink:href="data:image/png;base64,<?= base64_encode($lbinary) ?>" />

</svg>