<?php

include("include.php");
include("imagick_include.php");

$id = $_GET["id"];
$cacheno = $_GET["c"];

mysql_select_db("rpihockey");

$result = dbquery("SELECT * from players WHERE `id` = '$id'");
$row = mysql_fetch_array($result);

$result = dbquery("SELECT * from teams WHERE `name` = '" . $row["team"] ."'");
$teamrow = mysql_fetch_array($result);
$tColor = rgbhex($teamrow["colorr"],$teamrow["colorg"],$teamrow["colorb"]);

$stype = $row["stype"];
if($stype != "txt")
{
  $result = dbquery("SELECT * FROM stattype WHERE `type`  = '$stype'");
  $slabel = mysql_fetch_array($result);
}

mysql_select_db("rpits");

//
// CACHING SECTION
//

foreach($row as $item)
  $hash .= $item;
$key = $row["num"].$row["first"].$row["last"];
$hash = addslashes($hash);
$oldkey = $key;
$key = addslashes($key);

$result = dbquery("SELECT * from cache WHERE `key` = '$key'");
$cacherow = mysql_fetch_array($result);
$cacherow["hash"] = addslashes($cacherow["hash"]);

if($cacherow["hash"] == $hash && $cacheno != 1)
{
  $png = file_get_contents("out/$oldkey.png");
  if($png)
  {
    header('Content-Type: image/png');
    echo($png);
    exit();
  }
}


$canvas = new Imagick();
$canvas->newImage(1920,1080,"none","png");

blackbox($canvas,400,870,1120,160);
slantRectangle($canvas,360,800,780,80,$tColor);
slantRectangle($canvas,1100,800,150,80,"#303030");
slantRectangle($canvas,1210,800,130,80,$tColor);
slantRectangle($canvas,1300,800,140,80,"#303030");
slantRectangle($canvas,1400,800,160,80,"white");

$pPath = "teams/" . $row["team"] . "imgs/" . $row["first"] . $row["last"] . ".png";
$size = @getimagesize($pPath);

$pW = 192;
$pH = 230;
$pX = 400;
$pY = 801;

if($size[0]*1.2>$size[1])
{
  $pH = $size[1]/($size[0]/$pW);
  $pY += 230-$pH;
}

placeImage($canvas,$pX,$pY,$pW,$pH,$pPath);

placeImage($canvas,1442,802,76,76,"teamlogos/".$row["team"] . ".png");

shadowedText($canvas,560,805,535,70,$row["first"]. " " . $row["last"],"west","fontN","white");
shadowedText($canvas,1100,800,150,80,$row["num"],"center","fontN","white");
shadowedText($canvas,1210,800,130,80,$row["pos"],"center","fontN","white");
shadowedText($canvas,1300,800,140,80,$row["year"],"center","fontN","white");

$details = "Hometown: " . $row["hometown"] . "       Ht: " . $row["height"]. "       Wt: " . $row["weight"];
plainText($canvas,630,884,880,33,$details,"west","fontN","white");

if($stype != "txt")
{
  $i = 2;
  for(;strlen($slabel[$i])>0;$i++) {}
 
  $boxW = 880/($i-2);
  $totalwidths = 0;
  for($j=2;$j<$i;$j++)
    $totalwidths += getTextWidth($boxW,80,$row[$j+8],"fontN");
  
  
  //echo $totalwidths;
  $spacing = (880-$totalwidths)/($i-2);
  //plainText($canvas,50,50,300,50,$i-2 . ", $boxW, $totalwidths, $spacing ","left","fontN","white");
  $xpos = 650;

  for($j=2;$j<$i;$j++)
  {
    $thisWidth = getTextWidth($boxW,80,$row[$j+8],"fontN");
    $xpos -=($boxW-$thisWidth)/2;
    plainText($canvas,$xpos,915,$boxW,40,$slabel[$j],"center","fontN","white");
    plainText($canvas,$xpos,955,$boxW,80,$row[$j+8],"center","fontN","white");
    $xpos += ($boxW-$thisWidth)/2+$thisWidth+$spacing;
    
  }

  
}

dbquery("REPLACE INTO cache SET `key` = '$key', `hash` = '$hash';");

$filename = $row["num"].$row["first"].$row["last"];

$canvas->setImageDepth(8);

$canvas->writeImage('out/' . $filename . '.png');

$thumb = $canvas->clone();
$thumb->cropImage(318,239,398,794);
$thumb->resizeImage(53,40,Imagick::FILTER_TRIANGLE,1);
$thumb->writeImage('thumbs/' . $filename . '.png');

header("Content-Type: image/png");
echo $canvas;



?>
