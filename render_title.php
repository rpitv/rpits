<?
include("include.php");

$id = $_GET["id"];
$query = "SELECT titles.id,titles.name,titles.data,templates.w,templates.h,templates.path,templates.type FROM titles JOIN templates ON titles.template = templates.id WHERE titles.id=$id";
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
    $color = getColor($id,$c["color"],$im);
    imagefilledpolygon($im,$c["points"],count($c["points"])/2,$color);
  }
  elseif($c["command"] == "asset")
  {
    $asset = imagecreatefrompng($c["asset"]);
    imagecopy($im,$asset,$c["points"][0],$c["points"][1],0,0,$c["points"][2],$c["points"][3]);
  }
  elseif($c["command"] == "img")
  {
    $imgsrc = getContent($id,$c["asset"]);
    $img = imagecreatefrompng("$imgsrc");
    $size = getimagesize("$imgsrc");
    imagecopyresampled($im,$img,$c["points"][0],$c["points"][1],0,0,$c["points"][2],$c["points"][3],$size[0],$size[1]);
  }
  elseif($c["command"] == "text")
  {
    $content = getContent($id,$c["content"]);
    $font = getFont($c["font"]);
    if($c["align"] == "r")
    {
      $r = imagefttext($im, $c["size"], 0, 700, 0, $white, $font, $content);
      $c["points"][0] -= ($r[2]-$r[0]);
    }
    if($c["shadow"] > 0)
      imagefttext($im, $c["size"], 0, $c["points"][0]+$c["shadow"], $c["points"][1]+$c["shadow"], $black, $font, $content);
    imagefttext($im, $c["size"], 0, $c["points"][0], $c["points"][1], $white, $font, $content);
  }


}
header('Content-Type: image/png');
imagealphablending($im,false);
imagesavealpha($im,true);
//imagepng($im,("pngout/" . $row["filename"] . '.png'));
imagepng($im);
imagedestroy($im);


?>