<link rel="stylesheet" type="text/css" href="im_edit.css" media="screen" />
<!--<script src="js/jquery-1.5.1.min.js" type="text/javascript"></script>
<script src="js/jquery-ui-1.8.12.custom.min.js" type="text/javascript"></script>-->
<?php
include("include.php");

$titleId = $_GET["id"];

$titleResult = dbquery("SELECT * from titles where id=\"$titleId\" LIMIT 1;");
$titleRow = mysql_fetch_array($titleResult);

$templateId = $titleRow["template"];

$templateResult = dbquery("SELECT * from templates where id=\"$templateId\" LIMIT 1;");
$templateRow = mysql_fetch_array($templateResult);

$templateXML = fopen($templateRow["path"], "r");
$contents = stream_get_contents($templateXML);

function printEditableRow($row, $id, $type) {
	$val = $row[$type];
	$val = str_replace('\n', PHP_EOL, $val);
	$newlines = substr_count($val, PHP_EOL);
	$name = $row["name"];
	echo '<div class="row">';
	echo '<div class="label">' . $name . '</div>';
	echo '<div class="form"><form class="edit_form" action="javascript:true" method="GET">';
	echo '<input type="hidden" name="' . $id . '" value="' . $name . '" />';
	if ($newlines > 0) {
		echo '<textarea class="noHotkeys" rows="' . ($newlines + 1) . '" name="' . $type . '">' . "\n" . $val . '</textarea>';
	} else {
		echo '<input class="noHotkeys" type="text" name="' . $type . '" value="' . $val . '" />';
	}
	echo '<input class="submit noHotkeys" type="submit" value="Update" />';
	echo '</form></div>';
	echo '</div>';
}

$xml = new SimpleXMLElement($contents);
echo '<div id="editTitle">';
echo "<h3>Shadow Text</h3>";
foreach ($xml->overlay->shadowText as $text) {
	$t = dbFetch($titleId, $text);
	printEditableRow($t, $titleId, 'text');
}
echo "<h3>Normal Text</h3>";
foreach ($xml->overlay->plainText as $text) {
	$t = dbFetch($titleId, $text);
	printEditableRow($t, $titleId, 'text');
}
echo "<h3>Color Bars</h3>";

foreach ($xml->geo->slantRectangle as $slantRectangle) {
	$t = dbFetch($titleId, $slantRectangle);
	printEditableRow($t, $titleId, 'color');
}
echo "<h3>Images</h3>";

foreach ($xml->overlay->placeImage as $image) {
	$t = dbFetch($titleId, $image);
	printEditableRow($t, $titleId, 'path');
}

echo '</div>'
?><br style="clear:both" />
<button tid="<?= $titleId ?>" id="render" name="Render">Force Render</button>
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
        window.renderQueue.addToQueue(<?= $titleId ?>);
			}
		});
    return false;
	});
	$("#render").click(function() { // Force Render
    var button = $(this).html("Rendering");
    $.ajax({
      type: "GET",
      url: "im_render_title.php?id="+$(this).attr("tid"),
      success: function(data) {
        button.html("Done Rendering");
      }
    });
	});
</script>

