<?
$dir = $_GET["dir"];
$w = $_GET["w"];
if($dir)
{
  $handle = opendir($dir);
  while (false !== ($file = readdir($handle))) {
    if(!is_dir($file) && $file[0] == "I")
      echo("<img src=\"thumber.php?p=$dir&f=$file&w=$w\" />\n");
  }
}
else
{
echo("No gallery selected");
}
?>