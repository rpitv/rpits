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

$voffset = 130; // Vertical offset from top of frame to top of bar1
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
$voffset -= 130;

imagefilledpolygon($im,$team1bar,4,$teamcolor);
imagefilledpolygon($im,$team2bar,4,$team2color);
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