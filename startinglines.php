<?

mysql_connect("localhost","root","");
mysql_select_db("rpihockey");

$gid = $_GET["id"];

$query =  "SELECT * from `lines` WHERE `id` = '$gid'";
$result = mysql_query($query) or die("<b>YOU DID SOMETHING WRONG YOU IDIOT</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
$row = mysql_fetch_array($result);
$team = $row["team"];

$query = "SELECT * from teams WHERE `name` = '$team'";
$result = mysql_query($query) or die("<b>YOU DID SOMETHING WRONG YOU IDIOT</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
$teamrow = mysql_fetch_array($result);

// Create a 640x120 image, fill with transparency
$im = imagecreatetruecolor(640, 240);
imageantialias($im,true);
imagealphablending($im,false);
$col=imagecolorallocatealpha($im,255,255,255,127);
imagefilledrectangle($im,0,0,639,239,$col);
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

$teamcolor = imagecolorallocate($im, $teamrow["colorr"], $teamrow["colorg"], $teamrow["colorb"]);

$mainbar = array(
	50,178,
	608,178,
	589,217,
	30,217);

if($row["pos5"] == NULL)
{
  $numplayers = 4;
  $startx = 58 + 54;
}
else
{
  $numplayers = 5;
  $startx = 58;
}
if($row["pos4"] == NULL)
{
  $numplayers = 3;
  $startx = 58 + 107;
}



for($i = 1; $i <= $numplayers; $i++)
{
  $overlay = imagecreatefrompng('assets/frame.png');
  imagecopyresized($im,$overlay,$startx,55,0,0,98,125,98,125);
  $portraitw = 82;
  $portraith = 100;
  $query = "SELECT * from players WHERE `num` = '" . $row["p$i"] . "' AND `team` = '" . $row["team"] . "'";
  $result = mysql_query($query) or die("<b>YOU DID SOMETHING WRONG YOU IDIOT</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
  $prow = mysql_fetch_array($result);
  $portrait = @imagecreatefromjpeg("teams\\" . $prow["team"] . "imgs\\" . $prow["first"] . $prow["last"] . "HS.jpg");
  if(!$portrait){
	  $portrait = imagecreatefrompng("assets/nopic.png");
	  $teamrow["start"] = 143;
	  $teamrow["end"] = 175;
  }
  imagecopyresampled($im, $portrait, $startx+8, 82, 0, 0, $portraitw, $portraith, $teamrow["start"], $teamrow["end"]);
  $startx = $startx + 98+9;
}

imagefilledpolygon($im,$mainbar,4,$teamcolor);

$overlay = imagecreatefrompng('assets/linesoverlay.png');
imagecopyresized($im,$overlay,0,178,0,0,640,60,640,60);

if($row["pos5"] == NULL)
{
  $numplayers = 4;
  $startx = 106 + 54;
}
else
{
  $numplayers = 5;
  $startx = 106;
}
if($row["pos4"] == NULL)
{
  $numplayers = 3;
  $startx = 106 + 107;
}

$namesize = 12;
for($i = 1; $i <= $numplayers; $i++)
{
  $query = "SELECT * from players WHERE `num` = '" . $row["p$i"] . "' AND `team` = '" . $row["team"] . "'";
  $result = mysql_query($query) or die("<b>YOU DID SOMETHING WRONG YOU IDIOT</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
  $prow = mysql_fetch_array($result);

  $dummy = imagefttext($im, $namesize, 0, 700,0,$white,$arialb,$prow["first"]);
  imagefttext($im, $namesize, 0,$startx-($dummy[2]-$dummy[0])/2,196,$white,$arialb,$prow["first"]);
  $dummy = imagefttext($im, $namesize, 0, 700,0,$white,$arialb,$prow["last"]);
  imagefttext($im, $namesize, 0,$startx-($dummy[2]-$dummy[0])/2,211,$white,$arialb,$prow["last"]);
  $dummy = imagefttext($im, $namesize, 0, 700,0,$white,$arialb,($prow["num"] . " " . $prow["pos"]));
  imagefttext($im, $namesize, 0,$startx-($dummy[2]-$dummy[0])/2,78,$black,$arialb,($prow["num"] . " " . $prow["pos"]));
  $startx = $startx + 98+9;
}

$namesize = 14;
$dummy = imagefttext($im, $namesize, 0, 700,0,$white,$arialb,$row["label"]);
  imagefttext($im, $namesize, 0,320-($dummy[2]-$dummy[0])/2,233,$black,$arialb,$row["label"]);

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