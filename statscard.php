<?php

mysql_connect("localhost","root","");
mysql_select_db("rpihockey");

$gid = $_GET["id"];
$cacheno = $_GET["c"];

$query =  "SELECT * from players WHERE `id` = '$gid'";
$result = mysql_query($query) or die("<b>YOU DID SOMETHING WRONG YOU IDIOT</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
$row = mysql_fetch_array($result);

//
// CACHING SECTION
//

foreach($row as $item)
  $hash .= $item;
$key = $row["num"].$row["first"].$row["last"];
$hash = addslashes($hash);
$oldkey = $key;
$key = addslashes($key);

$query =  "SELECT * from cache WHERE `key` = '$key'";
$result = mysql_query($query) or die("<b>YOU DID SOMETHING WRONG YOU IDIOT</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
$cacherow = mysql_fetch_array($result);
$cacherow["hash"] = addslashes($cacherow["hash"]);

if($cacherow["hash"] == $hash && $cacheno != 1)
{
  $png = file_get_contents("pngout/$oldkey.png");
  if($png)
  {
    header('Content-Type: image/png');
    echo($png);
    exit();
  }
}

//
// AUX QUERIES FOR TEAM AND LABEL DATA (could be joins if I knew what I were doing)
//

$team = $row["team"];
$query = "SELECT * from teams WHERE `name` = '$team'";
$result = mysql_query($query) or die("<b>YOU DID SOMETHING WRONG YOU IDIOT</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
$teamrow = mysql_fetch_array($result);

$stype = $row["stype"];
if($stype != "txt")
{
  $query = "SELECT * FROM stattype WHERE `type`  = '$stype'";
  $result = mysql_query($query) or die("<b>YOU DID SOMETHING WRONG YOU IDIOT</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
  $slabel = mysql_fetch_array($result);
}

//
// CREATE IMAGE, INITIALIZE SOME CONSTANT VALUES
//

$im = imagecreatetruecolor(640, 120);
imageantialias($im,true);
imagealphablending($im,false);
$col=imagecolorallocatealpha($im,255,255,255,127);
imagefilledrectangle($im,0,0,639,159,$col);
imagealphablending($im,true);

$white = imagecolorallocate($im, 0xFF, 0xFF, 0xFF);
$font = 'fonts/GothamNarrow-Bold.otf';
$fontc = 'fonts/GothamNarrow-Bold.otf';
imageantialias($im,true);

//
// DETERMINE WHICH TYPE OF TITLE WE'RE CREATING BASED ON EXISTENCE OF PORTRAIT
//


$portrait_path = "teams/" . $row["team"] . "imgs/" . $row["first"] . $row["last"] . ".png";
$size = @getimagesize($portrait_path);

$portrait = @imagecreatefrompng($portrait_path);

$nopic = 0;
if(!$portrait)
{
  /*if($stype != 'f' && $stype != 'hn')
  {
    $portrait = imagecreatefrompng("assets/nopic.png");
    $size = getimagesize("assets/nopic.png");
    
  }
  else*/
    $nopic = 1;
}

//
// DRAW POLYGONS AND OVERLAY TITLE TEMPLATE
//


$teamcolor = imagecolorallocate($im, $teamrow["colorr"], $teamrow["colorg"], $teamrow["colorb"]);
$logocolor = imagecolorallocate($im, $teamrow["logor"], $teamrow["logog"], $teamrow["logob"]);
$mainbar = array(
  50,0,
  520,0,
  520,39,
  30,39
);
$numberbar = array(
  520,0,
  608,0,
  588,39,
  520,39);
if($stype == 'f' ||  $nopic == 1)
{
  $mainbar = array(
    50,60,
    520,60,
    520,99,
    30,99
  );
  $numberbar = array(
    520,60,
    608,60,
    588,99,
    520,99
  );
}
imagefilledpolygon($im,$mainbar,4,$teamcolor);
imagefilledpolygon($im,$numberbar,4,$logocolor);
/* // CODE FOR GRADIENTS
if ($row["team"] == "rpi")
{
  $rpibg = imagecreatefrompng('assets/rpi_whiteout_gradient.png');
  imagecopyresized($im,$rpibg,0,0,0,0,640,120,640,120);
}
*/

//
// ADD PLAYER PORTRAIT
//

$portraitw = 100;
$portraith = 120;
$portraitx = 50;
$portraity = 0;

$portrait_path = "teams/" . $row["team"] . "imgs/" . $row["first"] . $row["last"] . ".png";
$size = @getimagesize($portrait_path);

$portrait = @imagecreatefrompng($portrait_path);

$nopic = 0;
if(!$portrait)
{
  /*if($stype != 'f' && $stype != 'hn')
  {
    $portrait = imagecreatefrompng("assets/nopic.png");
    $size = getimagesize("assets/nopic.png");
    
  }
  else*/
    $nopic = 1;
}

///
/// Add Overlay
///

$overlay = imagecreatefrompng('assets/overlay.png');
if($stype == 'f' ||  $nopic == 1)
{ 
  if($nopic == 0)
    $overlay = imagecreatefrompng('assets/football_overlay.png');
  else
    $overlay = imagecreatefrompng('assets/football_overlay_nopic.png');
}
imagecopyresized($im,$overlay,0,0,0,0,640,120,640,120);

if($nopic == 0)
  imagecopyresampled($im, $portrait, $portraitx, $portraity, 0, 0, $portraitw, $portraith, $size[0], $size[1]);

//LAST SEASON GRAPHIC
/*if($row["team"] == "rpi" || $row["team"] == "msu" && $row["s1"] > 0)
{
  $season = imagecreatefrompng('assets\last_season.png');
  imagecopyresampled($im, $season, 0, 0, 0, 0, 640, 120, 640, 120);
}*/


//
// WRITE CONTENT
//

$numsize = 27;
if($stype == 'f')
  $numsize = 24;
$labelsize = 17;
$statssize = 28;
$v = 0;
$posa = 0;
$numa = 0;
if($stype == 'f' || $nopic == 1)
{
  $v=60;
  $posa = 8;
  $numa = 14;
}

// Output player number (autocenter)
$dummy = imagefttext($im, $numsize, 0, 700, 0, $white, $fontc, $row["num"]);
imagefttext($im, $numsize, 0, 428-($dummy[2]-$dummy[0])/2-$numa, 34+$v, $black, $fontc, $row["num"]);
imagefttext($im, $numsize, 0, 426-($dummy[2]-$dummy[0])/2-$numa, 32+$v, $white, $fontc, $row["num"]);

// Output player name (auto resize)
$namey = 32;
$nameychange = 0;
$namesize = 27;
$result = imagefttext($im, $namesize, 0, 720, 55, $black, $fontc, ($row["first"] . " " . $row["last"]));
$namemax = 255;
if($nopic == 1)
  $namemax = 330;
while($result[2] - $result[0] > $namemax)
{
  $namesize--;
  $result = imagefttext($im, $namesize, 0, 720, 55, $black, $fontc, ($row["first"] . " " . $row["last"]));
  $nameychange++;
}
$namey -= ($nameychange -1)/2;
$namecolor = $white;
$shadowcolor = $black;
$namex = 135;
if($nopic == 1)
  $namex = 60;
imagefttext($im, $namesize, 0, $namex+2, $namey+2+$v, $shadowcolor, $fontc, ($row["first"] . " " . $row["last"]));
imagefttext($im, $namesize, 0, $namex, $namey+$v, $namecolor, $fontc, ($row["first"] . " " . $row["last"]));

// Output player details (auto resize and auto weight)
$detailssize = 14;
if(!$row["weight"])
  $result = imagefttext($im, $detailssize, 0, 720, 55, $white, $font, ("Hometown: " . $row["hometown"] . "       Height: " . $row["height"]));
else
  $result = imagefttext($im, $detailssize, 0, 720, 55, $white, $font, ("Hometown: " . $row["hometown"] . "       Ht: " . $row["height"]. "       Wt: " . $row["weight"]));

$detailsmax = 410;
if($nopic == 1)
  $detailsmax = 505;
while($result[2] - $result[0] > $detailsmax)
{
  $detailssize--;
  if(!$row["weight"])
    $result = imagefttext($im, $detailssize, 0, 720, 55, $white, $font, ("Hometown: " . $row["hometown"] . "       Height: " . $row["height"]));
  else
    $result = imagefttext($im, $detailssize, 0, 720, 55, $white, $font, ("Hometown: " . $row["hometown"] . "       Ht: " . $row["height"]. "       Wt: " . $row["weight"]));
}
$detailsx = 170;
if($nopic == 1)
  $detailsx = 75;
if(!$row["weight"])
  $result = imagefttext($im, $detailssize, 0, $detailsx, 56+$v, $white, $font, ("Hometown: " . $row["hometown"] . "       Height: " . $row["height"]));
else
  $result = imagefttext($im, $detailssize, 0, $detailsx, 56+$v, $white, $font, ("Hometown: " . $row["hometown"] . "       Ht: " . $row["height"]. "       Wt: " . $row["weight"]));


// Output player position (auto center, and do some manual kerning)
$dummy = imagefttext($im, $numsize, 0, 700, 0, $white, $font, $row["pos"]);
if($row["pos"] == "G")
  $dummy[2] += 4;
imagefttext($im, $numsize, 0, 472-($dummy[2]-$dummy[0])/2-$posa,34+$v,$shadowcolor, $fontc, $row["pos"]);
imagefttext($im, $numsize, 0, 470-($dummy[2]-$dummy[0])/2-$posa,32+$v,$namecolor, $fontc, $row["pos"]);

// Output player year (auto center)
$dummy = imagefttext($im, $numsize, 0, 700, 0, $white, $font, $row["year"]);
imagefttext($im, $numsize, 0, 517-($dummy[2]-$dummy[0])/2,34+$v,$black, $fontc, $row["year"]);
imagefttext($im, $numsize, 0, 515-($dummy[2]-$dummy[0])/2,32+$v,$white, $fontc, $row["year"]);

//
// OUTPUT PLAYER STATS
//

if($stype != "txt" && $nopic == 0)
{

  $labelheight = 82;
  $statsheight = 113+$v;
  $result[2] = 170 - $slabel["spacing"];

  for($i=2;$slabel[$i];$i++)
  {
    $result = imagefttext($im, $labelsize, 0, $result[2]+$slabel["spacing"], $labelheight, $white, $font, $slabel[$i]);
    if($slabel[$i] == "+/-")
    {
      if($row[$i+8] > 0)
	$row[$i+8] = "+" . $row[$i+8];
    }
    $dummy = imagefttext($im, $statssize, 0, 700, $statsheight, $white, $font, $row[$i+8]);
    imagefttext($im, $statssize, 0, (($result[2]-$result[0])/2-($dummy[2]-$dummy[0])/2+$result[0]), $statsheight, $white, $font, $row[$i+8]);
  }
}
else
{
  $textsize = 18;
  $result = imagefttext($im, $textsize, 0, 170, 82, $white, $font, $row["s8"]);
}

//
// TEAM LOGO
//

$logo = imagecreatefrompng('teamlogos\\' . $teamrow["logo"]);
imagecopyresampled($im, $logo, 550, 0+$v, 0, 0, 40, 40, 100, 100);

//
// SAVE CACHE HACHE
//

$query = "REPLACE INTO cache SET `key` = '$key', `hash` = '$hash';";
$result = mysql_query($query) or die("<b>YOU DID SOMETHING WRONG YOU IDIOT</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());

//
// HEADERS AND FILE FORMATS
//

header('Content-Type: image/png');
imagealphablending($im,false);
imagesavealpha($im,true);
imagepng($im,("pngout/" . $row["num"] . $row["first"] . $row["last"] . '.png'));
imagepng($im);
imagedestroy($im);

$magick = new Imagick();
$magick->readimage(realpath("pngout/" . $row["num"] . $row["first"] . $row["last"] . '.png'));
$magick->setImageFormat( "tga" );
$magick->writeImage("out/" . $row["team"] . $row["num"] . $row["first"] . $row["last"] . '.tga');

?>