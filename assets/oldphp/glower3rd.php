<?php

mysql_connect("localhost","root","");
mysql_select_db("rpihockey");

$gid = $_GET["id"];

$query =  "SELECT * from goalies WHERE `id` = '$gid'";
$result = mysql_query($query) or die("<b>YOU DID SOMETHING WRONG YOU IDIOT</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
$row = mysql_fetch_array($result);

// Create a 720x120 image
$im = imagecreatetruecolor(720, 120);

// Initialize colors and fonts for ez
$red = imagecolorallocate($im, 0xFF, 0x00, 0x00);
$white = imagecolorallocate($im, 0xFF, 0xFF, 0xFF);
$black = imagecolorallocate($im, 0x00, 0x00, 0x00);
$grey = imagecolorallocate($im, 0x99, 0x99, 0x99);
$arial = 'fonts/Arial.ttf';
$arialb = 'fonts/arialbd.ttf';
$arialblk = 'fonts/ariblk.ttf';

// Do font size tests:
$namesize = 26;
$result = imagefttext($im, $namesize, 0, 97, 33, $black, $arialb, ($row["first"] . " " . $row["last"] . "    " . $row["num"] . "    " . $row["pos"] . "   " . $row["year"]));
while($result[2] - $result[0] > 430){
	$namesize--;
	$result = imagefttext($im, $namesize, 0, 97, 33, $black, $arialb, ($row["first"] . " " . $row["last"] . "    " . $row["num"] . "    " . $row["year"]));
	}
$detailssize = 12;
$result = imagefttext($im, $detailssize, 0, 155, 57, $white, $arial, ("Hometown: " . $row["hometown"] . "       Height: " . $row["height"] . "       Weight: " . $row["weight"]));
while($result[2] - $result[0] > 450){
	$detailssize--;
	$result = imagefttext($im, $detailssize, 0, 155, 57, $white, $arial, ("Hometown: " . $row["hometown"] . "       Height: " . $row["height"] . "       Weight: " . $row["weight"]));
	}





imagefilledrectangle($im, 0, 0, 719, 119, $white);
$bg = imagecreatefrompng($row["team"] . 'imgs/bg.png');
imagecopyresized($im, $bg, 0,0,0,0,720,120,720,120);

//405 = Max name size
//400 = Max details size
/*
// Make the background red
imagefilledrectangle($im, 0, 0, 719, 119, $grey);
imagefilledrectangle($im, 50, 0, 669, 119, $black);
imagefilledrectangle($im, 50, 0, 669, 39, $red);
imagefilledrectangle($im, 629, 1, 668, 38, $black);
*/

// Path to our ttf font file


// Draw the text 'PHP Manual' using font size 13
imagefttext($im, $namesize, 0, 97, 33, $black, $arialb, ($row["first"] . " " . $row["last"] . "   " . $row["num"] . "   " . $row["pos"] . "   " . $row["year"]));
imagefttext($im, $namesize, 0, 95, 31, $white, $arialb, ($row["first"] . " " . $row["last"] . "   " . $row["num"] . "   " . $row["pos"] . "   " . $row["year"]));
imagefttext($im, $detailssize, 0, 155, 57, $white, $arial, ("Hometown: " . $row["hometown"] . "       Ht: " . $row["height"] . "       Wt: " . $row["weight"]));
$result = imagefttext($im, 16, 0, 155, 83, $white, $arial, "Games");
imagefttext($im, 18, 0, (($result[2]-$result[0])/2-12+$result[0]), 110, $white, $arial, $row["gp"]);
$result = imagefttext($im, 16, 0, ($result[2]+20), 83, $white, $arial, "W");
imagefttext($im, 18, 0, (($result[2]-$result[0])/2-7+$result[0]), 110, $white, $arial, $row["win"]);
$result = imagefttext($im, 16, 0, ($result[2]+20), 83, $white, $arial, "L");
imagefttext($im, 18, 0, (($result[2]-$result[0])/2-9+$result[0]), 110, $white, $arial, $row["loss"]);
$result = imagefttext($im, 16, 0, ($result[2]+20), 83, $white, $arial, "T");
imagefttext($im, 18, 0, (($result[2]-$result[0])/2-7+$result[0]), 110, $white, $arial, $row["tie"]);
$result = imagefttext($im, 16, 0, ($result[2]+20), 83, $white, $arial, "Save %");
imagefttext($im, 18, 0, (($result[2]-$result[0])/2-30+$result[0]), 110, $white, $arial, $row["saveper"]);
$result = imagefttext($im, 16, 0, ($result[2]+20), 83, $white, $arial, "GAA");
imagefttext($im, 18, 0, (($result[2]-$result[0])/2-21+$result[0]), 110, $white, $arial, $row["gaa"]);

$logo = imagecreatefrompng('teamlogos\\' . $row["team"] . '.png');
imagecopyresized($im, $logo, 640-50-40, 0 , 0, 0, 40, 40, 40, 40);
$portrait = imagecreatefromjpeg($row["team"] . "imgs\\" . $row["first"] . $row["last"] . "HS.jpg");
//imagecopyresized($im, $portrait, 50, 0, 0, 0, 96, 120, 143, 175);
//imagecopyresized($im, $portrait, 75, 45, 0, 0, 56, 70, 143, 175);
imagecopyresized($im, $portrait, 70, 40, 0, 0, 64, 80, 64, 80);

$im2 = imagecreatetruecolor(720,120);
imagefilledrectangle($im2, 0, 0, 719, 119, $white);
imagecopyresized($im2, $im, 85,0,0,0,720,120,720,120);
$bug = imagecreatefrompng('static/bug.png');
imagecopyresized($im2, $bug, 0,50,0,0,720,120,720,120);

// Output image to the browser
header('Content-Type: image/png');
imagepng($im2);
imagepng($im2,($row["team"] . "out/" . $row["num"] . $row["first"] . $row["last"] . '.png'));


imagedestroy($im);
imagedestroy($im2);
?>