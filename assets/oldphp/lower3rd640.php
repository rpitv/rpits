<?php

mysql_connect("localhost","root","");
mysql_select_db("rpihockey");

$gid = $_GET["id"];

$query =  "SELECT * from players WHERE `id` = '$gid'";
$result = mysql_query($query) or die("<b>YOU DID SOMETHING WRONG YOU IDIOT</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
$row = mysql_fetch_array($result);
if($row["pos"] == 'G'){
	$query =  "SELECT * from goalies WHERE `last` = '" . $row["last"] . "'";
	$result = mysql_query($query) or die("<b>YOU DID SOMETHING WRONG YOU IDIOT</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
	$row = mysql_fetch_array($result);
}

// Create a 640x120 image
$im = imagecreatetruecolor(640, 120);
imagealphablending($im,false);
$col=imagecolorallocatealpha($im,255,255,255,127);
imagefilledrectangle($im,0,0,639,119,$col);
imagealphablending($im,true);


// Initialize colors and fonts for ez
$red = imagecolorallocate($im, 0xFF, 0x00, 0x00);
$white = imagecolorallocate($im, 0xFF, 0xFF, 0xFF);
$black = imagecolorallocate($im, 0x00, 0x00, 0x00);
$grey = imagecolorallocate($im, 0x99, 0x99, 0x99);
$arial = 'fonts/FRADMCN.TTF';
$arialb = 'fonts/FRADMCN.TTF';
/*$arial = 'fonts/Arial.ttf';
$arialb = 'fonts/arialbd.ttf';*/
$arialblk = 'fonts/ariblk.ttf';

// Do font size tests:
$namesize = 26;
$result = imagefttext($im, $namesize, 0, 97, 33, $black, $arialb, ($row["num"] . "   " . $row["first"] . " " . $row["last"] . "    " . $row["pos"] . "   " . $row["year"]));
while($result[2] - $result[0] > 340){
	$namesize--;
	$result = imagefttext($im, $namesize, 0, 97, 33, $black, $arialb, ($row["first"] . " " . $row["last"] . "    " . $row["num"] . "    " . $row["year"]));
	}
$detailssize = 12;
if($row["weight"] == "0"){
	$result = imagefttext($im, $detailssize, 0, 155, 57, $white, $arial, ("Hometown: " . $row["hometown"] . "       Height: " . $row["height"]));
	while($result[2] - $result[0] > 400){
		$detailssize--;
		$result = imagefttext($im, $detailssize, 0, 155, 57, $white, $arial, ("Hometown: " . $row["hometown"] . "       Height: " . $row["height"]));
	}
} else {
	$result = imagefttext($im, $detailssize, 0, 155, 57, $white, $arial, ("Hometown: " . $row["hometown"] . "       Height: " . $row["height"] . "       Weight: " . $row["weight"]));
	while($result[2] - $result[0] > 400){
		$detailssize--;
		$result = imagefttext($im, $detailssize, 0, 155, 57, $white, $arial, ("Hometown: " . $row["hometown"] . "       Height: " . $row["height"] . "       Weight: " . $row["weight"]));
	}
}


/*if($row["team"][strlen($row["team"])-1] = "w"){
	$row["team"] = substr($row["team"],0,-1);
}
*/
$bg = imagecreatefrompng($row["team"] . 'imgs/bg.png');
imagecopyresized($im, $bg, 0,0,0,0,720,120,720,120);


imagefttext($im, $namesize, 0, 97, 33, $black, $arialb, ($row["num"] . "   " . $row["first"] . " " . $row["last"] . "   " . $row["pos"] . "   " . $row["year"]));
imagefttext($im, $namesize, 0, 95, 31, $white, $arialb, ($row["num"] . "   " . $row["first"] . " " . $row["last"] . "   " . $row["pos"] . "   " . $row["year"]));
if($row["weight"] == "0"){
	imagefttext($im, $detailssize, 0, 155, 57, $white, $arial, ("Hometown: " . $row["hometown"] . "       Height: " . $row["height"]));
} else {
	imagefttext($im, $detailssize, 0, 155, 57, $white, $arial, ("Hometown: " . $row["hometown"] . "       Height: " . $row["height"] . "       Wt: " . $row["weight"]));
}

if($row["pos"] == 'G'){
	$result = imagefttext($im, 13, 0, 155, 83, $white, $arial, "Games");
	imagefttext($im, 18, 0, (($result[2]-$result[0])/2-12+$result[0]), 110, $white, $arial, $row["gp"]);
	$result = imagefttext($im, 13, 0, ($result[2]+22), 83, $white, $arial, "W");
	imagefttext($im, 18, 0, (($result[2]-$result[0])/2-7+$result[0]), 110, $white, $arial, $row["win"]);
	$result = imagefttext($im, 13, 0, ($result[2]+22), 83, $white, $arial, "L");
	imagefttext($im, 18, 0, (($result[2]-$result[0])/2-9+$result[0]), 110, $white, $arial, $row["loss"]);
	$result = imagefttext($im, 13, 0, ($result[2]+22), 83, $white, $arial, "T");
	imagefttext($im, 18, 0, (($result[2]-$result[0])/2-7+$result[0]), 110, $white, $arial, $row["tie"]);
	$result = imagefttext($im, 13, 0, ($result[2]+22), 83, $white, $arial, "Save %");
	imagefttext($im, 18, 0, (($result[2]-$result[0])/2-30+$result[0]), 110, $white, $arial, $row["saveper"]);
	$result = imagefttext($im, 13, 0, ($result[2]+22), 83, $white, $arial, "GAA");
	imagefttext($im, 18, 0, (($result[2]-$result[0])/2-21+$result[0]), 110, $white, $arial, $row["gaa"]);
} else {
	$result = imagefttext($im, 13, 0, 155, 83, $white, $arial, "Games");
	imagefttext($im, 18, 0, (($result[2]-$result[0])/2-12+$result[0]), 110, $white, $arial, $row["gp"]);
	$result = imagefttext($im, 13, 0, ($result[2]+15), 83, $white, $arial, "Goals");
	imagefttext($im, 18, 0, (($result[2]-$result[0])/2-8+$result[0]), 110, $white, $arial, $row["g"]);
	$result = imagefttext($im, 13, 0, ($result[2]+15), 83, $white, $arial, "Assists");
	imagefttext($im, 18, 0, (($result[2]-$result[0])/2-12+$result[0]), 110, $white, $arial, $row["a"]);
	$result = imagefttext($im, 13, 0, ($result[2]+15), 83, $white, $arial, "Points");
	imagefttext($im, 18, 0, (($result[2]-$result[0])/2-12+$result[0]), 110, $white, $arial, $row["pts"]);
	$result = imagefttext($im, 13, 0, ($result[2]+15), 83, $white, $arial, "PIM");
	imagefttext($im, 18, 0, (($result[2]-$result[0])/2-11+$result[0]), 110, $white, $arial, $row["pen"]);
}


$logo = imagecreatefrompng('teamlogos\\' . $row["team"] . '.png');
imagecopyresized($im, $logo, 640-50-40-80, 0 , 0, 0, 40, 40, 40, 40);
$portrait = imagecreatefromjpeg($row["team"] . "imgs\\" . $row["first"] . $row["last"] . "HS.jpg");
imagecopyresampled($im, $portrait, 70, 40, 0, 0, 64, 80, 143, 175);

$im2 = imagecreatetruecolor(640,120);
imagealphablending($im2,false);
$col=imagecolorallocatealpha($im2,255,255,255,127);
imagefilledrectangle($im2,0,0,639,119,$col);
imagealphablending($im2,true);
//imagefilledrectangle($im2, 0, 0, 719, 119, $white);
imagecopyresized($im2, $im, 85,0,0,0,720,120,720,120);
$bug = imagecreatefrompng('static/bug.png');
imagecopyresized($im2, $bug, 0,50,0,0,720,120,720,120);

// Output image to the browser

header('Content-Type: image/png');
imagealphablending($im2,false);
imagesavealpha($im2,true);
imagepng($im2);
imagepng($im2,($row["team"] . "out/" . $row["num"] . $row["first"] . $row["last"] . '.png'));





imagedestroy($im);
imagedestroy($im2);
?>