<?
include("include.php");

$id = $_GET["id"];
$query = "SELECT templates.w,templates.h,templates.path,templates.type FROM templates WHERE templates.id=$id";
$result = dbquery($query);
$title_row = mysql_fetch_assoc($result);

$im = imagecreatetruecolor($title_row["w"], $title_row["h"]);
imageantialias($im,true);
imagealphablending($im,false);
$alpha=imagecolorallocatealpha($im,255,255,255,127);
imagefilledrectangle($im,0,0,$title_row["w"],$title_row["h"],$alpha);
imagealphablending($im,true);

$white = imagecolorallocate($im,255,255,255);
$black = imagecolorallocate($im,0,0,0);

if($title_row["type"] == "text")
  $commands = getLinesFromText($title_row["path"]);
else
  $commands = array();

foreach($commands as $c)
{
  if($c["command"] == "poly")
  {
    $color = $white;
    imagefilledpolygon($im,$c["points"],count($c["points"])/2,$color);
  }
  elseif($c["command"] == "asset")
  {
    $asset = imagecreatefrompng($c["asset"]);
    imagecopy($im,$asset,$c["points"][0],$c["points"][1],0,0,$c["points"][2],$c["points"][3]);
  }
}
header('Content-Type: image/png');
imagealphablending($im,false);
imagesavealpha($im,true);
imagepng($im);
imagedestroy($im);


?>