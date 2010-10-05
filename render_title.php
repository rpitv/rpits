<?
include("include.php");

$id = $_GET["id"];
$query = "SELECT titles.id,titles.name,titles.data,templates.w,templates.h,templates.path FROM titles JOIN templates ON titles.template = templates.id WHERE titles.id=$id";
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

$h_template = fopen($title_row["path"],"r");
while(!feof($h_template))
{
  $t_line = preg_split("/[\s\n]/",fgets($h_template));
  if($t_line[0] == "poly")
  {
    $color = getColor($id,$t_line[2],$im);
    $points = preg_split("/[,;]/",$t_line[1]);
    imagefilledpolygon($im,$points,count($points)/2,$color);
  }
  elseif($t_line[0] == "asset")
  {
    $asset = imagecreatefrompng($t_line[1]);
    $p = preg_split("/[,;]/",$t_line[2]);
    imagecopy($im,$asset,$p[0],$p[1],0,0,$p[2],$p[3]);
  }
  elseif($t_line[0] == "img")
  {
    $imgsrc = getContent($id,$t_line[1]);
    $img = imagecreatefrompng("$imgsrc");
    $p = preg_split("/[,;]/",$t_line[2]);
    $size = getimagesize("$imgsrc");
    imagecopyresampled($im,$img,$p[0],$p[1],0,0,$p[2],$p[3],$size[0],$size[1]);
  }
  elseif($t_line[0] == "text")
  {
    $content = getContent($id,$t_line[1]);
    $font = getFont($t_line[7]);
    $p = preg_split("/[,;]/",$t_line[6]);
    if($t_line[3] == "r")
    {
      $r = imagefttext($im, $t_line[4], 0, 700, 0, $white, $font, $content);
      $p[0] -= ($r[2]-$r[0]);
    }
    if($t_line[2] == "s")
      imagefttext($im, $t_line[4], 0, $p[0]+$t_line[5], $p[1]+$t_line[5], $black, $font, $content);
    imagefttext($im, $t_line[4], 0, $p[0], $p[1], $white, $font, $content);
  }


}
header('Content-Type: image/png');
imagealphablending($im,false);
imagesavealpha($im,true);
//imagepng($im,("pngout/" . $row["filename"] . '.png'));
imagepng($im);
imagedestroy($im);


?>