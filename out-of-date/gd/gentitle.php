<?php

mysql_connect("localhost","root","");
mysql_select_db("rpihockey");

$gid = $_GET["id"];

$query =  "SELECT * from general WHERE `id` = '$gid'";
$result = mysql_query($query) or die("<b>YOU DID SOMETHING WRONG YOU IDIOT</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
$row = mysql_fetch_array($result);

foreach($row as $item)
  $hash .= $item;
$key = $row["filename"];
$hash = addslashes($hash);
$oldkey = $key;
$key = addslashes($key);

$query =  "SELECT * from cache WHERE `key` = '$key'";
$result = mysql_query($query) or die("<b>YOU DID SOMETHING WRONG YOU IDIOT</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
$cacherow = mysql_fetch_array($result);
$cacherow["hash"] = addslashes($cacherow["hash"]);

if($cacherow["hash"] == $hash)
{
  $png = file_get_contents("pngout/$oldkey.png");
  if($png)
  {
    header('Content-Type: image/png');
    echo($png);
    exit();
  }
}

// Create image
$im = imagecreatetruecolor(640, $row["height"]);
imageantialias($im,true);
imagealphablending($im,false);
$col=imagecolorallocatealpha($im,255,255,255,127);
imagefilledrectangle($im,0,0,639,$row["height"],$col);
imagealphablending($im,true);

// Initialize colors and fonts for ez
$red = imagecolorallocate($im, 0xFF, 0x00, 0x00);
$white = imagecolorallocate($im, 0xFF, 0xFF, 0xFF);
$black = imagecolorallocate($im, 0x00, 0x00, 0x00);
$grey = imagecolorallocate($im, 0x99, 0x99, 0x99);
$darkgrey = imagecolorallocate($im, 0x15, 0x15, 0x15);
$lightgrey = imagecolorallocate($im, 0xDD, 0xDD, 0xDD);
$fontc = 'fonts/GothamXNarrow-Bold.otf';
$font = 'fonts/GothamNarrow-Bold.otf';
$fontb = 'fonts/Gotham-Bold.ttf';
imageantialias($im,true);

if($row["team"][1]) {
  $team = $row["team"];
  $query = "SELECT * from teams WHERE `name` = '$team'";
  $result = mysql_query($query) or die("<b>YOU DID SOMETHING WRONG YOU IDIOT</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
  $teamrow = mysql_fetch_array($result);
  $teamcolor = imagecolorallocate($im, $teamrow["colorr"], $teamrow["colorg"], $teamrow["colorb"]);
}
if($row["team2"][1]) {
  $team2 = $row["team2"];
  $query = "SELECT * from teams WHERE `name` = '$team2'";
  $result = mysql_query($query) or die("<b>YOU DID SOMETHING WRONG YOU IDIOT</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
  $team2row = mysql_fetch_array($result);
  $team2color = imagecolorallocate($im, $team2row["colorr"], $team2row["colorg"], $team2row["colorb"]);
}

$titlesize = 25;
$textsize = 21;

// "Normal" title - user selectable height
if($row["special"] != 1 && $row["special"] != 5) {
  $greybg = imagecreatefrompng('assets/greybg.png');
  imagecopyresized($im,$greybg,60,59,0,0,520,$row["height"]-59,10,10);

  $redbar = array(
      80,20,	//Top left
      60,59,	//Bottom left
      579,59,	//Bottom Right
      599,20);	//Top right
  imagefilledpolygon($im,$redbar,4,$teamcolor);
  $barborder = imagecreatefrompng('assets/normalbar.png');
  imagecopy($im,$barborder,60,20,0,0,540,40);

  $titlex = 100;

  if($row["logo"] == "1") {
    $logobar = array(
	70,9,	//Top left
	40,68,	//Bottom left
	128,68,	//Bottom Right
	158,9);	//Top right
    $logocolor = imagecolorallocate($im, $teamrow["logor"], $teamrow["logog"], $teamrow["logob"]);
    imagefilledpolygon($im,$logobar,4,$logocolor);
    $logoborder = imagecreatefrompng('assets/logobox.png');
    imagecopy($im,$logoborder,40,9,0,0,120,60);
    $logo = imagecreatefrompng('teamlogos\\' . $row["team"] . '.png');
    if($row["special"]=="3")
      imagecopyresampled($im, $logo, 60, 0, 0, 0, 80, 80, 100, 100);
    else
      imagecopyresampled($im, $logo, 70, 10, 0, 0, 58, 58, 100, 100);
    $titlex = 165;
  }
  // Normal title, no variants
  if($row["special"]=="0") {
    imagefttext($im, $titlesize, 0, $titlex, 53, $black, $font, ($row["title"]));
    imagefttext($im, $titlesize, 0, $titlex-2, 51, $white, $font, ($row["title"]));
    imagefttext($im, $textsize, 0, 75, 98, $white, $font, ($row["content"]));
    // Starting line
  } elseif ($row["special"]=="4") {
    imagefttext($im, $titlesize, 0, $titlex, 53, $black, $font, ($row["title"]));
    imagefttext($im, $titlesize, 0, $titlex-2, 51, $white, $font, ($row["title"]));
    imagefttext($im, $textsize, 0, 80, 103, $white, $font, ($row["content"]));
    imagefttext($im, $textsize, 0, 140, 103, $white, $font, ($row["col1"]));
    imagefttext($im, $textsize, 0, 190, 103, $white, $font, ($row["col2"]));
    imagefttext($im, $textsize, 0, 480, 103, $white, $font, ($row["col3"]));
    // ECAC Standings
  } elseif ($row["special"]=="3") {
    $textsize = 17;
    imagefttext($im, $titlesize, 0, $titlex, 53, $black, $font, ($row["title"]));
    imagefttext($im, $titlesize, 0, $titlex-2, 51, $white, $font, ($row["title"]));
    imagefttext($im, $textsize, 0, 80, 87, $white, $font, ($row["content"]));
    imagefttext($im, $textsize, 0, 260, 87, $white, $font, ($row["col1"]));
    imagefttext($im, $textsize, 0, 360, 87, $white, $font, ($row["col2"]));
    imagefttext($im, $textsize, 0, 440, 87, $white, $font, ($row["col3"]));
    // Smaller text
  } elseif($row["special"] == "99") {
    //$textsize = 16;
    imagefttext($im, $titlesize, 0, $titlex, 53, $black, $font, ($row["title"]));
    imagefttext($im, $titlesize, 0, $titlex-2, 51, $white, $font, ($row["title"]));
    imagefttext($im, $textsize, 0, 80, 85, $white, $font, ($row["content"]));
    // Stats (auto center)
  } elseif ($row["special"]=="2") {
    $logobar = array(
	70,9,	//Top left
	40,68,	//Bottom left
	128,68,	//Bottom Right
	158,9);	//Top right
    $logocolor = imagecolorallocate($im, $teamrow["logor"], $teamrow["logog"], $teamrow["logob"]);
    imagefilledpolygon($im,$logobar,4,$logocolor);
    $logoborder = imagecreatefrompng('assets/logobox.png');
    imagecopy($im,$logoborder,40,9,0,0,120,60);
    $logo = imagecreatefrompng('teamlogos\\' . $row["team"] . '.png');
    imagecopyresampled($im, $logo, 71, 11, 0, 0, 58, 58, 100, 100);
    $titlex = 165;
    $logobar = array(
	520,9,	//Top left
	490,68,	//Bottom left
	578,68,	//Bottom Right
	608,9);	//Top right
    $logocolor = imagecolorallocate($im, $team2row["logor"], $team2row["logog"], $team2row["logob"]);
    imagefilledpolygon($im,$logobar,4,$logocolor);
    $logoborder = imagecreatefrompng('assets/logobox.png');
    imagecopy($im,$logoborder,490,9,0,0,120,60);
    $logo = imagecreatefrompng('teamlogos\\' . $row["team2"] . '.png');
    imagecopyresampled($im, $logo, 521, 11, 0, 0, 58, 58, 100, 100);
    imagefttext($im, $titlesize, 0, $titlex, 53, $black, $font, ($row["title"]));
    imagefttext($im, $titlesize, 0, $titlex-2, 51, $white, $font, ($row["title"]));
    $awayteamstats = explode("\n", $row["content"]);
    $statslabels = explode("\n", $row["col1"]);
    $hometeamstats = explode("\n", $row["col2"]);
    $liney = 103;
    foreach($awayteamstats as $awayline) {
      $loc = imagefttext($im, $textsize, 0, 700, 103, $white, $font, $awayline);
      imagefttext($im, $textsize, 0, (130-($loc[2]-$loc[0])/2), $liney, $white, $font, $awayline);
      $liney += 40;
    }
    $liney = 143;
    foreach($statslabels as $statlabel) {
      $loc = imagefttext($im, $textsize, 0, 700, 103, $white, $font, $statlabel);
      imagefttext($im, $textsize, 0, (320-($loc[2]-$loc[0])/2), $liney, $white, $font, $statlabel);
      $liney += 40;
    }
    $liney = 103;
    foreach($hometeamstats as $homeline) {
      $loc = imagefttext($im, $textsize, 0, 700, 103, $white, $font, $homeline);
      imagefttext($im, $textsize, 0, (500-($loc[2]-$loc[0])/2), $liney, $white, $font, $homeline);
      $liney += 40;
    }
  }
}
// Score Lower 3rd
if($row["special"] == 5) {
  $voffset = 80; // Vertical offset from top of frame to top of bar1
  $team1bar = array(
      173,276,
      503,276,
      478,325,
      148,325);
  $team2bar = array(
      173,355,
      503,355,
      478,404,
      148,404);

  imagefilledpolygon($im,$team1bar,4,$teamcolor);
  imagefilledpolygon($im,$team2bar,4,$team2color);
  $frame = imagecreatefrompng('assets/scoreframe.png');
  imagecopy($im,$frame,0,0,0,0,640,480);

  $logo1 = imagecreatefrompng('teamlogos\\' . $teamrow["logo"]);
  imagecopyresampled($im, $logo1, 67, 262, 0, 0, 75, 75, 100, 100);
  $logo2 = imagecreatefrompng('teamlogos\\' . $team2row["logo"]);
  imagecopyresampled($im, $logo2, 67, 342, 0, 0, 75, 75, 100, 100);
  imagefttext($im, 30, 0, 183, 317, $black, $font, $row["content"]);
  imagefttext($im, 30, 0, 180, 314, $white, $font, $row["content"]);
  imagefttext($im, 30, 0, 183, 397, $black, $font, $row["col2"]);
  imagefttext($im, 30, 0, 180, 394, $white, $font, $row["col2"]);
  $dummy = imagefttext($im, 60, 0, 700, 328, $black, $fontb, $row["col1"]);
  imagefttext($im, 60, 0, 532-($dummy[2]-$dummy[0])/2, 331, $black, $fontb, $row["col1"]);
  imagefttext($im, 60, 0, 529-($dummy[2]-$dummy[0])/2, 328, $white, $fontb, $row["col1"]);
  $dummy = imagefttext($im, 60, 0, 700, 328, $black, $fontb, $row["col3"]);
  imagefttext($im, 60, 0, 532-($dummy[2]-$dummy[0])/2, 411, $black, $fontb, $row["col3"]);
  imagefttext($im, 60, 0, 529-($dummy[2]-$dummy[0])/2, 408, $white, $fontb, $row["col3"]);
  $dummy = imagefttext($im, 18, 0, 700, 328, $black, $fontb, $row["title"]);
  imagefttext($im, 18, 0, 313-($dummy[2]-$dummy[0])/2, 447, $black, $font, $row["title"]);
  imagefttext($im, 18, 0, 310-($dummy[2]-$dummy[0])/2, 444, $white, $font, $row["title"]);
}
// Opening Title
if($row["special"] == 1) {
  $voffset = 80; // Vertical offset from top of frame to top of bar1
  $team1bar = array(
      220,$voffset,
      598,$voffset,
      568,$voffset+59,
      190,$voffset+59);
  $spacing = 130;
  $voffset += $spacing; // Spacing between top of bar 1 and top of bar 2
  $team2bar = array(
      220,$voffset,
      598,$voffset,
      568,$voffset+59,
      190,$voffset+59);
  $voffset -= $spacing;
  
  $locationbar = array(
      75,390,
      388,390,
      373,420,
      60,420);

  imagefilledpolygon($im,$team1bar,4,$teamcolor);
  imagefilledpolygon($im,$team2bar,4,$team2color);
  imagefilledpolygon($im,$locationbar,4,$team2color);
  $frame = imagecreatefrompng('assets/opening_frame.png');
  imagecopy($im,$frame,0,0,0,0,640,480);

  $logo1 = imagecreatefrompng('teamlogos\\' . $teamrow["logo"]);
  imagecopyresampled($im, $logo1, 88, $voffset, 0, 0, 90, 90, 100, 100);
  $logo2 = imagecreatefrompng('teamlogos\\' . $team2row["logo"]);
  imagecopyresampled($im, $logo2, 88, $voffset+$spacing, 0, 0, 90, 90, 100, 100);
  $team1info = explode(",",$row["content"]);
  $team2info = explode(",",$row["col1"]);
  imagefttext($im, 45, 0, 218, $voffset+53, $black, $fontc, $team1info[0]);
  imagefttext($im, 45, 0, 215, $voffset+50, $white, $fontc, $team1info[0]);
  imagefttext($im, 45, 0, 218, $voffset+53+$spacing, $black, $fontc, $team2info[0]);
  imagefttext($im, 45, 0, 215, $voffset+50+$spacing, $white, $fontc, $team2info[0]);
  imagefttext($im, 19, 0, 197, $voffset+84, $black, $font, $team1info[1]);
  imagefttext($im, 19, 0, 195, $voffset+82, $white, $font, $team1info[1]);
  imagefttext($im, 19, 0, 197, $voffset+84+$spacing, $black, $font, $team2info[1]);
  imagefttext($im, 19, 0, 195, $voffset+82+$spacing, $white, $font, $team2info[1]);
  $r1 = imagefttext($im, 19, 0, 700, $voffset+82, $white, $fontc, $team1info[2]);
  $r2 = imagefttext($im, 19, 0, 700, $voffset+84+$spacing, $black, $fontc, $team2info[2]);
  if($r1[2]>$r2[2])
    $hoffset = 505 - ($r1[2]-$r1[0]);
  else
    $hoffset = 505 - ($r2[2]-$r2[0]);
  imagefttext($im, 19, 0, $hoffset+2, $voffset+84, $black, $fontc, $team1info[2]);
  imagefttext($im, 19, 0, $hoffset, $voffset+82, $white, $fontc, $team1info[2]);
  imagefttext($im, 19, 0, $hoffset+2, $voffset+84+$spacing, $black, $fontc, $team2info[2]);
  imagefttext($im, 19, 0, $hoffset, $voffset+82+$spacing, $white, $fontc, $team2info[2]);
  $arenainfo = explode(",",$row["col2"]);
  imagefttext($im, 19, 0, 82, 415, $black, $font, $arenainfo[0]);
  imagefttext($im, 19, 0, 80, 413, $white, $font, $arenainfo[0]);
  imagefttext($im, 19, 0, 67, 445, $black, $font, $row["col3"]);
  imagefttext($im, 19, 0, 65, 443, $white, $font, $row["col3"]);
  imagefttext($im, 19, 0, 192, 445, $black, $font, $arenainfo[1]);
  imagefttext($im, 19, 0, 190, 443, $white, $font, $arenainfo[1]);
}

$query = "REPLACE INTO cache SET `key` = '$key', `hash` = '$hash';";
$result = mysql_query($query) or die("<b>YOU DID SOMETHING WRONG YOU IDIOT</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());


header('Content-Type: image/png');
imagealphablending($im,false);
imagesavealpha($im,true);
$savepath = "pngout/" . $row["filename"] . '.png';
imagepng($im,$savepath);
header('Content-Type: image/png');
imagepng($im);

imagedestroy($im);

echo ("???");
flush();


$magick = new Imagick();
$magick->readimage(realpath("pngout/" . $row["filename"] . '.png'));
$magick->setImageFormat( "tga" );
//$magick->scaleImage(480,$row["height"]);
//$magick->borderImage(none,80,0);
$magick->writeImage("out/" . $row["filename"] . '.tga');

?>