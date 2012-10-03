<?php

mysql_connect("localhost","root","");
mysql_select_db("rpihockey");

$gid = $_GET["id"];

$query =  "SELECT * from general WHERE `id` = '$gid'";
$result = mysql_query($query) or die("<b>YOU DID SOMETHING WRONG YOU IDIOT</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
$row = mysql_fetch_array($result);

// Create image
$im = imagecreatetruecolor(640, 480);
imageantialias($im,true);
imagealphablending($im,false);
$col=imagecolorallocatealpha($im,255,255,255,127);
imagefilledrectangle($im,0,0,639,479,$col);
imagealphablending($im,true);

// Initialize colors and fonts for ez
$white = imagecolorallocate($im, 0xFF, 0xFF, 0xFF);
$black = imagecolorallocate($im, 0,0,0);
$fontb = 'fonts/Gotham-Bold.ttf';
$font = 'fonts/GothamNarrow-Bold.otf';
$fontc = 'fonts/GothamXNarrow-Bold.otf';
imageantialias($im,true);

$team = $row["team"];
$query = "SELECT * from teams WHERE `name` = '$team'";
$result = mysql_query($query) or die("<b>YOU DID SOMETHING WRONG YOU IDIOT</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
$teamrow = mysql_fetch_array($result);
$teamcolor = imagecolorallocate($im, $teamrow["colorr"], $teamrow["colorg"], $teamrow["colorb"]);
$team2 = $row["team2"];
$query = "SELECT * from teams WHERE `name` = '$team2'";
$result = mysql_query($query) or die("<b>YOU DID SOMETHING WRONG YOU IDIOT</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
$team2row = mysql_fetch_array($result);
$team2color = imagecolorallocate($im, $team2row["colorr"], $team2row["colorg"], $team2row["colorb"]);
//$team2color = imagecolorallocate($im, $team2row["colorr"]+30, $team2row["colorg"]+30, $team2row["colorb"]+30);

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

	
header('Content-Type: image/png');
imagealphablending($im,false);
imagesavealpha($im,true);
imagepng($im,("pngout/" . $row["filename"] . '.png'));
imagepng($im);
imagedestroy($im);

$magick = new Imagick();
$magick->readimage("pngout/" . $row["filename"] . '.png');
$magick->setImageFormat( "tga" );
$magick->writeImage("out/" . $row["filename"] . '.tga');
?>