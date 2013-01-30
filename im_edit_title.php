<link rel="stylesheet" type="text/css" href="im_edit.css" media="screen" />
<!--<script src="js/jquery-1.5.1.min.js" type="text/javascript"></script>
<script src="js/jquery-ui-1.8.12.custom.min.js" type="text/javascript"></script>-->
<?php

include("include.php");

$title_id = $_GET["id"];

$result = dbquery("SELECT * from titles where id=\"$title_id\" LIMIT 1;");
$titleRow = mysql_fetch_array($result);

$template_id = $titleRow["template"];

$result = dbquery("SELECT * from templates where id=\"$template_id\" LIMIT 1;");
$templateRow = mysql_fetch_array($result);

$templateXML = fopen($templateRow["path"],"r");
$contents = stream_get_contents($templateXML);

$xml = new SimpleXMLElement($contents);

$lc = "<div id=\"left-column\">\n";
$rc = "<div id=\"right-column\">\n";
$lc .="<h3>Shadow Text</h3>\n";
$rc .="<h3>&nbsp</h3>\n";
foreach($xml->overlay->shadowText as $text)
{
  $t = dbFetch($title_id,$text);
  $lc .= "<p>".$t["name"]."</p>\n";
  $rc .= "<form class=\"edit_form\" action\"javascript:true\" \"method=\"GET\">";
  $rc .= "\t<input type=\"hidden\" name=\"" . $title_id . "\" value=\"" . $t["name"] . "\" />";
  $rc .= "\t<input type=\"text\" name=\"text\" value=\"" . $t["text"] . "\" />\n";
  $rc .= "\t<input class=\"submit\" type=\"submit\" value=\"Update\" />";
  $rc .= "</form>\n";
}
$lc .="<h3>Normal Text</h3>\n";
$rc .="<h3>&nbsp</h3>\n";
foreach($xml->overlay->plainText as $text)
{
  $t = dbFetch($title_id,$text);
  $lc .= "<p>".$t["name"]."</p>\n";
  $rc .= "<form class=\"edit_form\" action\"javascript:true\" \"method=\"GET\">";
  $rc .= "\t<input type=\"hidden\" name=\"" . $title_id . "\" value=\"" . $t["name"] . "\" />";
  $rc .= "\t<input type=\"text\" name=\"text\" value=\"" . $t["text"] . "\" />\n";
  $rc .= "\t<input class=\"submit\" type=\"submit\" value=\"Update\" />";
  $rc .= "</form>\n";
}
$lc .="<h3>Color Bars</h3>\n";
$rc .="<h3>&nbsp</h3>\n";
foreach($xml->geo->slantRectangle as $slantRectangle)
{
  $t = dbFetch($title_id,$slantRectangle);
  $lc .= "<p>".$t["name"]."</p>\n";
  $rc .= "<form class=\"edit_form\" action\"javascript:true\" \"method=\"GET\">";
  $rc .= "\t<input type=\"hidden\" name=\"" . $title_id . "\" value=\"" . $t["name"] . "\" />";
  $rc .= "\t<input type=\"text\" name=\"color\" value=\"" . $t["color"] . "\" />\n";
  $rc .= "\t<input class=\"submit\" type=\"submit\" value=\"Update\" />";
  $rc .= "</form>\n";
}
$lc .="<h3>Images</h3>\n";
$rc .="<h3>&nbsp</h3>\n";
foreach($xml->overlay->placeImage as $image)
{
  $t = dbFetch($title_id,$image);
  $lc .= "<p>".$t["name"]."</p>\n";
  $rc .= "<form class=\"edit_form\" action\"javascript:true\" \"method=\"GET\">";
  $rc .= "\t<input type=\"hidden\" name=\"" . $title_id . "\" value=\"" . $t["name"] . "\" />";
  $rc .= "\t<input type=\"text\" name=\"path\" value=\"" . $t["path"] . "\" />\n";
  $rc .= "\t<input class=\"submit\" type=\"submit\" value=\"Update\" />";
  $rc .= "</form>\n";
}

$lc .= "</div>\n";
$rc .= "</div>\n";

echo $lc . $rc;
?><br style="clear:both" />
<button tid="<?= $title_id ?>" id="render" name="Render" style="width:200px;margin-bottom:70px;margin-top:70px;" >Render</button>
<script type="text/javascript">
$(".edit_form").submit(function() {
  var form = $(this);
  form.children("input:last").attr("value", "Submitting");
    $.ajax({
        type: "POST",
        url: "cdb_update.php",
        data: $(this).serializeArray(),
        success: function(data) {
          form.children("input:last").attr("value", data);
          
          /*var startMilliseconds = new Date().getTime();
          $.ajax({
            type:"GET",
            url: "render_template.php",
            success: function(){
              var response = new Date().getTime() - startMilliseconds
              form.children("input:last").attr("value", "Updated - Done - " + response + " ms");
            }
          });*/       
        }
        });
    return false;
});
$("#render").click(function() {
  var button = $(this).html("Rendering");
    $.ajax({
        type: "GET",
        url: "im_render_title.php?id="+$(this).attr("tid"),
        //data: $(this).serializeArray(),
        success: function(data) {
          button.html("Done Rendering");
          
          /*var startMilliseconds = new Date().getTime();
          $.ajax({
            type:"GET",
            url: "render_template.php",
            success: function(){
              var response = new Date().getTime() - startMilliseconds
              form.children("input:last").attr("value", "Updated - Done - " + response + " ms");
            }
          });*/       
        }
        });
    return false;
});
</script>

