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

// Create a 640x120 image
$im = imagecreatetruecolor(640, 160);
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
$arial = 'fonts/FRADMCN.TTF';
$arialb = 'fonts/FRADMCN.TTF';
imageantialias($im,true);

$teamcolor = imagecolorallocate($im, $teamrow["colorr"], $teamrow["colorg"], $teamrow["colorb"]);

$namesize = 28;
$result = imagefttext($im, $namesize, 0, 120, 55, $black, $arialb, ($row["first"] . " " . $row["last"] . "   " . $row["pos"] . "   " . $row["year"]));
while($result[2] - $result[0] > 360){
	$namesize--;
	$result = imagefttext($im, $namesize, 0, 120, 55, $black, $arialb, ($row["first"] . " " . $row["last"] . "   " . $row["pos"] . "   " . $row["year"]));
}
$detailssize = 16;
if($teamrow["womens"] == "1"){
	$result = imagefttext($im, $detailssize, 0, 120, 55, $white, $arial, ("Hometown: " . $row["hometown"] . "       Height: " . $row["height"]));
} else {
	$result = imagefttext($im, $detailssize, 0, 120, 55, $white, $arial, ("Hometown: " . $row["hometown"] . "       Ht: " . $row["height"]. "       Wt: " . $row["weight"]));
}
while($result[2] - $result[0] > 410){
	$detailssize--;
	if($teamrow["womens"] == "1"){
		$result = imagefttext($im, $detailssize, 0, 120, 55, $white, $arial, ("Hometown: " . $row["hometown"] . "       Height: " . $row["height"]));
	} else {
		$result = imagefttext($im, $detailssize, 0, 120, 55, $white, $arial, ("Hometown: " . $row["hometown"] . "       Ht: " . $row["height"]. "       Wt: " . $row["weight"]));
	}
}


$greybg = imagecreatefrompng('new/greybg.png');
imagecopyresized($im,$greybg,60,64,0,0,520,100,10,10);

$redbar = array(
	80,20,	//Top left
	60,63,	//Bottom left
	577,63,	//Bottom Right
	599,20);	//Top right
imagefilledpolygon($im,$redbar,4,$teamcolor);
$blackbar = array(
	80,20,	//Top left
	60,63,	//Bottom left
	192,63,	//Bottom Right
	214,20);	//Top right
imagefilledpolygon($im,$blackbar,4,$darkgrey);
$barborder = imagecreatefrompng('new/mainbar.png');
imagecopy($im,$barborder,60,20,0,0,541,44);

$logobar = array(
	72,9,	//Top left
	41,70,	//Bottom left
	128,70,	//Bottom Right
	158,9);	//Top right
$logocolor = imagecolorallocate($im, $teamrow["logor"], $teamrow["logog"], $teamrow["logob"]);
imagefilledpolygon($im,$logobar,4,$logocolor);
$logoborder = imagecreatefrompng('new/logobox.png');
imagecopy($im,$logoborder,40,9,0,0,120,64);
$numsize = 36;
$labelsize = 18;
$statssize = 24;

if($row["num"][1] || $row["num"][1] == "0"){
	imagefttext($im, $numsize, 0, 151, 59, $black, $arialb, $row["num"]);
	imagefttext($im, $numsize, 0, 149, 57, $white, $arialb, $row["num"]);
} else {
	imagefttext($im, $numsize, 0, 164, 59, $black, $arialb, $row["num"]);
	imagefttext($im, $numsize, 0, 162, 57, $white, $arialb, $row["num"]);
}

imagefttext($im, $namesize, 0, 215, 55, $black, $arialb, ($row["first"] . " " . $row["last"] . "   " . $row["pos"] . "   " . $row["year"]));
imagefttext($im, $namesize, 0, 213, 53, $white, $arialb, ($row["first"] . " " . $row["last"] . "   " . $row["pos"] . "   " . $row["year"]));
if($teamrow["womens"] == "1"){
	$result = imagefttext($im, $detailssize, 0, 155, 87, $white, $arial, ("Hometown: " . $row["hometown"] . "       Height: " . $row["height"]));
/*} else if($row["team"] == "career"){
	$detailssize = 16;
	$result = imagefttext($im, $detailssize, 0, 155, 87, $white, $arial, "Career Totals:");
	*/
} else {
	$result = imagefttext($im, $detailssize, 0, 155, 87, $white, $arial, ("Hometown: " . $row["hometown"] . "       Ht: " . $row["height"]. "       Wt: " . $row["weight"]));
}

$labelheight = 115;
$statsheight = 145;

if($row["pos"] == 'G'){
	$result = imagefttext($im, $labelsize, 0, 155, $labelheight, $white, $arial, "Games");
	$dummy = imagefttext($im, $statssize, 0, 700, $statsheight, $white, $arial, $row["gp"]);
	imagefttext($im, $statssize, 0, (($result[2]-$result[0])/2-($dummy[2]-$dummy[0])/2+$result[0]), $statsheight, $white, $arial, $row["gp"]);

	$result = imagefttext($im, $labelsize, 0, ($result[2]+30), $labelheight, $white, $arial, "W");
	$dummy = imagefttext($im, $statssize, 0, 700, $statsheight, $white, $arial, $row["win"]);
	imagefttext($im, $statssize, 0, (($result[2]-$result[0])/2-($dummy[2]-$dummy[0])/2+$result[0]), $statsheight, $white, $arial, $row["win"]);

	$result = imagefttext($im, $labelsize, 0, ($result[2]+30), $labelheight, $white, $arial, "L");
	$dummy = imagefttext($im, $statssize, 0, 700, $statsheight, $white, $arial, $row["loss"]);
	imagefttext($im, $statssize, 0, (($result[2]-$result[0])/2-($dummy[2]-$dummy[0])/2+$result[0]), $statsheight, $white, $arial, $row["loss"]);

	$result = imagefttext($im, $labelsize, 0, ($result[2]+30), $labelheight, $white, $arial, "T");
	$dummy = imagefttext($im, $statssize, 0, 700, $statsheight, $white, $arial, $row["tie"]);
	imagefttext($im, $statssize, 0, (($result[2]-$result[0])/2-($dummy[2]-$dummy[0])/2+$result[0]), $statsheight, $white, $arial, $row["tie"]);

	$result = imagefttext($im, $labelsize, 0, ($result[2]+30), $labelheight, $white, $arial, "Save %");
	$dummy = imagefttext($im, $statssize, 0, 700, $statsheight, $white, $arial, $row["saveper"]);
	imagefttext($im, $statssize, 0, (($result[2]-$result[0])/2-($dummy[2]-$dummy[0])/2+$result[0]), $statsheight, $white, $arial, $row["saveper"]);

	$result = imagefttext($im, $labelsize, 0, ($result[2]+30), $labelheight, $white, $arial, "GAA");
	$dummy = imagefttext($im, $statssize, 0, 700, $statsheight, $white, $arial, $row["gaa"]);
	imagefttext($im, $statssize, 0, (($result[2]-$result[0])/2-($dummy[2]-$dummy[0])/2+$result[0]), $statsheight, $white, $arial, $row["gaa"]);
} else {
  if($row["team"] == "career"){
	  $result = imagefttext($im, $labelsize, 0, 155, $labelheight, $white, $arial, "Career\nTotals:");
	  $result = imagefttext($im, $labelsize, 0, ($result[2]+15), $labelheight, $white, $arial, "Games");
	  $dummy = imagefttext($im, $statssize, 0, 700, $statsheight, $white, $arial, $row["gp"]);
	  imagefttext($im, $statssize, 0, (($result[2]-$result[0])/2-($dummy[2]-$dummy[0])/2+$result[0]), $statsheight, $white, $arial, $row["gp"]);
  } else {

	$result = imagefttext($im, $labelsize, 0, 155, $labelheight, $white, $arial, "Games");
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
	$result = imagefttext($im, $labelsize, 0, ($result[2]+15), $labelheight, $white, $arial, "PIM");
	$dummy = imagefttext($im, $statssize, 0, 700, $statsheight, $white, $arial, $row["pen"]);
	imagefttext($im, $statssize, 0, (($result[2]-$result[0])/2-($dummy[2]-$dummy[0])/2+$result[0]), $statsheight, $white, $arial, $row["pen"]);
  }
}

$logo = imagecreatefrompng('teamlogos\\' . $row["team"] . '.png');
imagecopyresampled($im, $logo, 71, 11, 0, 0, 58, 58, 100, 100);

$portraitw = 71;
$portraith = 87;
$portraitx = 72;
$portraity = 73;

if($row["team"] == "harvardw"){
	$portrait = imagecreatefromjpeg($row["team"] . "imgs\\" . $row["first"] . $row["last"] . "HS.jpg");
	imagecopyresampled($im, $portrait, $portraitx, $portraity, 0, 0, $portraitw, $portraith, 133, 163);
} else if($row["team"] == "union"){
	$portrait = imagecreatefromjpeg($row["team"] . "imgs\player_" . $row["last"] . "HS08web.jpg");
	imagecopyresampled($im, $portrait, $portraitx, $portraity, 0, 0, $portraitw, $portraith, 125, 153);
} else if($row["team"] == "rpi" || $row["team"] == "career") {
	$portrait = imagecreatefrompng($row["team"] . "imgs\\" . $row["first"] . $row["last"] . ".png");
	imagecopyresampled($im, $portrait, $portraitx, $portraity, 0, 0, $portraitw, $portraith, 143, 175);
} else if($row["team"] == "stl") {
	$portrait = imagecreatefromjpeg($row["team"] . "imgs\\" . $row["first"] . $row["last"] . ".jpg");
	imagecopyresampled($im, $portrait, $portraitx, $portraity, 0, 0, $portraitw, $portraith, 70, 87);
} else if($row["team"] == "brown") {
	$portrait = imagecreatefromjpeg($row["team"] . "imgs\\" . $row["first"] . $row["last"] . "HS.jpg");
	imagecopyresampled($im, $portrait, $portraitx, $portraity, 0, 0, $portraitw, $portraith, 105, 130);
} else {
	$portrait = imagecreatefromjpeg($row["team"] . "imgs\\" . $row["first"] . $row["last"] . "HS.jpg");
	imagecopyresampled($im, $portrait, $portraitx, $portraity, 0, 0, $portraitw, $portraith, 143, 175);
}



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