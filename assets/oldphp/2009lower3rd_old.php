<?php

mysql_connect("localhost","root","");
mysql_select_db("rpihockey");

$gid = $_GET["id"];

$query =  "SELECT * from players WHERE `id` = '$gid'";
$result = mysql_query($query) or die("<b>YOU DID SOMETHING WRONG YOU IDIOT</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
$row = mysql_fetch_array($result);
if($row["pos"] == 'G'){
	$query =  "SELECT * from goalies WHERE `last` = '" . $row["last"] . "' AND `team` = '" . $row["team"] . "'";
	$result = mysql_query($query) or die("<b>YOU DID SOMETHING WRONG YOU IDIOT</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
	$row = mysql_fetch_array($result);
}
$team = $row["team"];
$query = "SELECT * from teams WHERE `name` = '$team'";
$result = mysql_query($query) or die("<b>YOU DID SOMETHING WRONG YOU IDIOT</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
$teamrow = mysql_fetch_array($result);

// Create a 640x120 image, fill with transparency
$im = imagecreatetruecolor(640, 120);
imageantialias($im,true);
imagealphablending($im,false);
$col=imagecolorallocatealpha($im,255,255,255,127);
imagefilledrectangle($im,0,0,639,159,$col);
imagealphablending($im,true);



// Initialize colors and fonts for ez
$red = imagecolorallocate($im, 0xFF, 0x00, 0x00);
$white = imagecolorallocate($im, 0xFF, 0xFF, 0xFF);
$black = imagecolorallocate($im, 0x00, 0x00, 0x00);
$grey = imagecolorallocate($im, 0x99, 0x99, 0x99);
$darkgrey = imagecolorallocate($im, 0x15, 0x15, 0x15);
$lightgrey = imagecolorallocate($im, 0xDD, 0xDD, 0xDD);
$arial = 'fonts/Gotham-Bold.ttf';
$arialb = 'fonts/Gotham-Bold.ttf';
imageantialias($im,true);

// BEGIN BACKGROUND SECTION

$teamcolor = imagecolorallocate($im, $teamrow["colorr"], $teamrow["colorg"], $teamrow["colorb"]);
$logocolor = imagecolorallocate($im, $teamrow["logor"], $teamrow["logog"], $teamrow["logob"]);

$mainbar = array(
	50,0,
	520,0,
	520,39,
	50,39,
	31,19);
$numberbar = array(
	520,0,
	589,0,
	608,19,
	589,39,
	520,39);

imagefilledpolygon($im,$mainbar,5,$teamcolor);
imagefilledpolygon($im,$numberbar,5,$logocolor);


$overlay = imagecreatefrompng('2009/overlay.png');
imagecopyresized($im,$overlay,0,0,0,0,640,120,640,120);

// BEGIN PORTRAIT SECTION

$portraitw = 100;
$portraith = 120;
$portraitx = 50;
$portraity = 0;

$portrait = @imagecreatefrompng($row["team"] . "imgs\\" . $row["first"] . $row["last"] . ".png");
if(!$portrait){
	$portrait = imagecreatefrompng("2009/nopic.png");
	$teamrow["start"] = 143;
	$teamrow["end"] = 175;
}
imagecopyresampled($im, $portrait, $portraitx, $portraity, 0, 0, $portraitw, $portraith, $teamrow["start"], $teamrow["end"]);

/*
$season = imagecreatefrompng('2009\last_season.png');
imagecopyresampled($im, $season, 0, 0, 0, 0, 640, 120, 640, 120);
*/
// BEGIN FONT SECTION

$numsize = 22;
if($teamrow["womens"] == "2" || $teamrow["womens"] == "3")
	$labelsize = 17;
else
	$labelsize = 18;
$statssize = 24;

$dummy = imagefttext($im, $numsize, 0, 700, 0, $white, $arial, $row["num"]);
imagefttext($im, $numsize, 0, 429-($dummy[2]-$dummy[0])/2, 32, $black, $arialb, $row["num"]);
imagefttext($im, $numsize, 0, 427-($dummy[2]-$dummy[0])/2, 30, $white, $arialb, $row["num"]);

$namesize = 24;
$result = imagefttext($im, $namesize, 0, 720, 55, $black, $arialb, ($row["first"] . " " . $row["last"] . "   " . $row["pos"] . "   " . $row["year"]));
while($result[2] - $result[0] > 360){
	$namesize--;
	$result = imagefttext($im, $namesize, 0, 720, 55, $black, $arialb, ($row["first"] . " " . $row["last"] . "   " . $row["pos"] . "   " . $row["year"]));
}

$detailssize = 11;
if($teamrow["womens"] == "1" || $teamrow["womens"] == "3" ){
	$result = imagefttext($im, $detailssize, 0, 720, 55, $white, $arial, ("Hometown: " . $row["hometown"] . "       Height: " . $row["height"]));
} else {
	$result = imagefttext($im, $detailssize, 0, 720, 55, $white, $arial, ("Hometown: " . $row["hometown"] . "       Ht: " . $row["height"]. "       Wt: " . $row["weight"]));
}
while($result[2] - $result[0] > 410){
	$detailssize--;
	if($teamrow["womens"] == "1" || $teamrow["womens"] == "3"){
		$result = imagefttext($im, $detailssize, 0, 720, 55, $white, $arial, ("Hometown: " . $row["hometown"] . "       Height: " . $row["height"]));
	} else {
		$result = imagefttext($im, $detailssize, 0, 720, 55, $white, $arial, ("Hometown: " . $row["hometown"] . "       Ht: " . $row["height"]. "       Wt: " . $row["weight"]));
	}
}

imagefttext($im, $namesize, 0, 137, 32, $black, $arialb, ($row["first"] . " " . $row["last"]));
imagefttext($im, $namesize, 0, 135, 30, $white, $arialb, ($row["first"] . " " . $row["last"]));
$dummy = imagefttext($im, $numsize, 0, 700, 0, $white, $arial, $row["pos"]);
imagefttext($im, $numsize, 0, 470-($dummy[2]-$dummy[0])/2,32,$black, $arialb, $row["pos"]);
imagefttext($im, $numsize, 0, 468-($dummy[2]-$dummy[0])/2,30,$white, $arialb, $row["pos"]);
$dummy = imagefttext($im, $numsize, 0, 700, 0, $white, $arial, $row["year"]);
imagefttext($im, $numsize, 0, 514-($dummy[2]-$dummy[0])/2,32,$black, $arialb, $row["year"]);
imagefttext($im, $numsize, 0, 512-($dummy[2]-$dummy[0])/2,30,$white, $arialb, $row["year"]);

if($teamrow["womens"] == "1" || $teamrow["womens"] == "3"){
	$result = imagefttext($im, $detailssize, 0, 170, 56, $white, $arial, ("Hometown: " . $row["hometown"] . "       Height: " . $row["height"]));
} else {
	$result = imagefttext($im, $detailssize, 0, 170, 56, $white, $arial, ("Hometown: " . $row["hometown"] . "       Ht: " . $row["height"]. "       Wt: " . $row["weight"]));
}

$labelheight = 82;
$statsheight = 113;

if($row["pos"] == 'G'){
	$result = imagefttext($im, $labelsize, 0, 170, $labelheight, $white, $arial, "Games");
	$dummy = imagefttext($im, $statssize, 0, 700, $statsheight, $white, $arial, $row["gp"]);
	imagefttext($im, $statssize, 0, (($result[2]-$result[0])/2-($dummy[2]-$dummy[0])/2+$result[0]), $statsheight, $white, $arial, $row["gp"]);

	$result = imagefttext($im, $labelsize, 0, ($result[2]+25), $labelheight, $white, $arial, "W");
	$dummy = imagefttext($im, $statssize, 0, 700, $statsheight, $white, $arial, $row["win"]);
	imagefttext($im, $statssize, 0, (($result[2]-$result[0])/2-($dummy[2]-$dummy[0])/2+$result[0]), $statsheight, $white, $arial, $row["win"]);

	$result = imagefttext($im, $labelsize, 0, ($result[2]+25), $labelheight, $white, $arial, "L");
	$dummy = imagefttext($im, $statssize, 0, 700, $statsheight, $white, $arial, $row["loss"]);
	imagefttext($im, $statssize, 0, (($result[2]-$result[0])/2-($dummy[2]-$dummy[0])/2+$result[0]), $statsheight, $white, $arial, $row["loss"]);

	$result = imagefttext($im, $labelsize, 0, ($result[2]+25), $labelheight, $white, $arial, "T");
	$dummy = imagefttext($im, $statssize, 0, 700, $statsheight, $white, $arial, $row["tie"]);
	imagefttext($im, $statssize, 0, (($result[2]-$result[0])/2-($dummy[2]-$dummy[0])/2+$result[0]), $statsheight, $white, $arial, $row["tie"]);

	$result = imagefttext($im, $labelsize, 0, ($result[2]+25), $labelheight, $white, $arial, "Save %");
	$dummy = imagefttext($im, $statssize, 0, 700, $statsheight, $white, $arial, $row["saveper"]);
	imagefttext($im, $statssize, 0, (($result[2]-$result[0])/2-($dummy[2]-$dummy[0])/2+$result[0]), $statsheight, $white, $arial, $row["saveper"]);

	$result = imagefttext($im, $labelsize, 0, ($result[2]+25), $labelheight, $white, $arial, "GAA");
	$dummy = imagefttext($im, $statssize, 0, 700, $statsheight, $white, $arial, $row["gaa"]);
	imagefttext($im, $statssize, 0, (($result[2]-$result[0])/2-($dummy[2]-$dummy[0])/2+$result[0]), $statsheight, $white, $arial, $row["gaa"]);
} else {
  if($row["team"] == "career"){
	  $result = imagefttext($im, $labelsize, 0, 170, $labelheight, $white, $arial, "Career\nTotals:");
	  $result = imagefttext($im, $labelsize, 0, ($result[2]+15), $labelheight, $white, $arial, "Games");
	  $dummy = imagefttext($im, $statssize, 0, 700, $statsheight, $white, $arial, $row["gp"]);
	  imagefttext($im, $statssize, 0, (($result[2]-$result[0])/2-($dummy[2]-$dummy[0])/2+$result[0]), $statsheight, $white, $arial, $row["gp"]);
	} else {
	$result = imagefttext($im, $labelsize, 0, 170, $labelheight, $white, $arial, "Games");
	$dummy = imagefttext($im, $statssize, 0, 700, $statsheight, $white, $arial, $row["gp"]);
	imagefttext($im, $statssize, 0, (($result[2]-$result[0])/2-($dummy[2]-$dummy[0])/2+$result[0]), $statsheight, $white, $arial, $row["gp"]);
	}
	$result = imagefttext($im, $labelsize, 0, ($result[2]+15), $labelheight, $white, $arial, "Goals");
	$dummy = imagefttext($im, $statssize, 0, 700, $statsheight, $white, $arial, $row["g"]);
	imagefttext($im, $statssize, 0, (($result[2]-$result[0])/2-($dummy[2]-$dummy[0])/2+$result[0]), $statsheight, $white, $arial, $row["g"]);

	$result = imagefttext($im, $labelsize, 0, ($result[2]+15), $labelheight, $white, $arial, "Assists");
	$dummy = imagefttext($im, $statssize, 0, 700, $statsheight, $white, $arial, $row["a"]);
	imagefttext($im, $statssize, 0, (($result[2]-$result[0])/2-($dummy[2]-$dummy[0])/2+$result[0]), $statsheight, $white, $arial, $row["a"]);

	$result = imagefttext($im, $labelsize, 0, ($result[2]+15), $labelheight, $white, $arial, "Points");
	$dummy = imagefttext($im, $statssize, 0, 700, $statsheight, $white, $arial, $row["pts"]);
	imagefttext($im, $statssize, 0, (($result[2]-$result[0])/2-($dummy[2]-$dummy[0])/2+$result[0]), $statsheight, $white, $arial, $row["pts"]);
  if($row["team"] != "career"){
	  if($teamrow["womens"] == "2" || $teamrow["womens"] == "3")
		$result = imagefttext($im, $labelsize, 0, ($result[2]+15), $labelheight, $white, $arial, "Shots");
	  else
		$result = imagefttext($im, $labelsize, 0, ($result[2]+15), $labelheight, $white, $arial, "PIM");
	$dummy = imagefttext($im, $statssize, 0, 700, $statsheight, $white, $arial, $row["pen"]);
	imagefttext($im, $statssize, 0, (($result[2]-$result[0])/2-($dummy[2]-$dummy[0])/2+$result[0]), $statsheight, $white, $arial, $row["pen"]);
  }
}

$logo = imagecreatefrompng('teamlogos\\' . $row["team"] . '.png');
imagecopyresampled($im, $logo, 550, 0, 0, 0, 40, 40, 100, 100);

header('Content-Type: image/png');
imagealphablending($im,false);
imagesavealpha($im,true);
imagepng($im,("pngout/" . $row["num"] . $row["first"] . $row["last"] . '.png'));
imagepng($im);
imagedestroy($im);

$magick = new Imagick();
$magick->readimage("pngout/" . $row["num"] . $row["first"] . $row["last"] . '.png');
$magick->setImageFormat( "tga" );
$magick->writeImage("out/" . $row["team"] . $row["num"] . $row["first"] . $row["last"] . '.tga');

?>