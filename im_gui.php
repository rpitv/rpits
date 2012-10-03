<?php

include("include.php");
include("imagick_include.php");

$update = $_GET["update"];
$id = $_GET["id"];
$name = $_GET["name"];
$x = $_GET["x"];
$y = $_GET["y"];
$w = $_GET["w"];
$h = $_GET["h"];
$load = $_GET["load"];

if($update)
{
  $attr = array();
  if($update == "pos")
    $attr = array("x"=>$x,"y"=>$y);
  if($update == "size")
  {
    $attr = array("w"=>$w,"h"=>$h,"x"=>$x,"y"=>$y);
  }
  foreach($attr as $key=>$value)
  {
    $query = "REPLACE into cdb (`title_id`,`name`,`key`,`value`) VALUES (\"$id\",\"$name\",\"$key\",\"$value\");";
    echo($query);
    dbquery($query);
  }
}

if($load == "attrs")
{
  $result = dbFetchAll($id,$name);
  echo("<h2>".$result["name"]."</h2>\n");
  unset($result["name"]);
  foreach($result as $key=>$value)
  {
    $o = "";
    $o .= "<form id=\"edit_form\" action\"javascript:true\" \"method=\"GET\">";
    $o .= $key .": ";
    $o .= "\t<input type=\"hidden\" name=\"" . $id . "\" value=\"" . $name . "\" />";
    $o .= "\t<input class=\"info\" type=\"text\" id=\"info-$key\" name=\"$key\" value=\"" . $value . "\" />\n";
    $o .= "</form>\n";
    echo $o;
  }
  ?>
<script type="text/javascript">
$("form").submit(function() {
  var form = $(this);
    $.ajax({
        type: "POST",
        url: "cdb_update.php",
        data: $(this).serializeArray(),
        success: function(data) {
          form.children("input:last").removeClass("outdated")
          form.children("input:last").addClass("updated")
        }
        });
    return false;
});
$("input").change(function(){
  $(this).removeClass("updated");
  $(this).addClass("outdated");
});
</script>
<?
}
else if ($load == "layers")
{
  
}


?>
