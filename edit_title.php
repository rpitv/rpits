<?
include("include.php");
$id = $_GET["id"];

$query = "SELECT titles.id,titles.name,titles.data,templates.w,templates.h,templates.path,templates.type FROM titles JOIN templates ON titles.template = templates.id WHERE titles.id=$id";
$result = dbquery($query);
$row = mysql_fetch_assoc($result);

if($row["type"] == "text")
  $commands = getLinesFromText($row["path"]);
else
  $commands = array();

?>
<style type="text/css">
  .floater
  {
    position:absolute;
    background-color: white;
    opacity:.8;
    margin:0px;
    border-style: solid;
    border-color:#00FF00;
    border-width: 1px;
}
.body
{
  margin:0px;
  padding:0px;
}
.input
{
  border-style:none;
  background:transparent;
}
</style>
<div style="height:480px;width:640px;position:relative">
  <div style="position:absolute;height:480px;width:43px;background-color:#FF7722;opacity:.3;left:0px;top:0px;"></div>
  <div style="position:absolute;height:480px;width:43px;background-color:#FF7722;opacity:.3;right:0px;top:0px;"></div>
  <div style="position:absolute;height:25px;width:640px;background-color:#FF7722;opacity:.3;left:0px;top:0px;"></div>
  <div style="position:absolute;height:25px;width:640px;background-color:#FF7722;opacity:.3;left:0px;bottom:0px;"></div>
<img style="position:absolute;bottom:0px;<? if($row["h"] < 455){?>margin-bottom:25px<? } ?>" width="640" src="render_title.php?id=<? echo($row["id"]); ?>" />
</div>
<div id=""style="background-image:url(render_template.php?id=<? echo($id); ?>);width:<? echo($row["w"]); ?>;height:<? echo($row["h"]); ?>;position:relative;">
<?
foreach($commands as $c)
{
  if($c["command"] == "text")
  {
    $content = getContent($id,$c["content"]);
    $font = getFontFamily($c["font"]);
    if($c["align"] == "r")
    {
      $align="right";
      $dist=640-$c["points"][0];
    }
    else
    {
      $align="left";
      $dist=$c["points"][0];
    }

    echo("<div class=\"floater\" style=\"$align:".$dist."px;top:".($c["points"][1]-($c["size"]))."px;height:".($c["size"]-($c["size"]/4))."pt;width:".$c["max"]."px\">\n");
    echo("\t<input class=\"input\" type=\"text\" style=\"font-family:$font;");
    echo("font-size:".$c["size"]."pt;text-align:$align;margin-top:-".($c["size"]/3)."px;width:".$c["max"]."px\" value=\"$content\"/>\n</div>\n");
    //imagefttext($im, $c["size"], 0, $c["points"][0], $c["points"][1], $white, $font, $content);
  }
}
?>
</div>
