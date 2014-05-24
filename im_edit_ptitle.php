<link rel="stylesheet" type="text/css" href="im_edit.css" media="screen" />
<!--<script src="js/jquery-1.5.1.min.js" type="text/javascript"></script>
<script src="js/jquery-ui-1.8.12.custom.min.js" type="text/javascript"></script>-->
<?php
include("include.php");

$titleId = $_GET["id"];

$titleResult = dbquery("SELECT * from players where id=\"$titleId\" LIMIT 1;");
$titleRow = mysql_fetch_array($titleResult);

$stype = $titleRow["stype"];
if ($stype != "txt") {
	$titleResult = dbquery("SELECT * FROM stattype WHERE `type` = '$stype'");
	$slabel = mysql_fetch_array($titleResult);
}

function printEditableRow($id, $row, $value) {
	$val = $row[$value];
	$val = str_replace('\n', PHP_EOL, $val);
	$newlines = substr_count($val, PHP_EOL);

	echo '<div class="row">';
	echo '<div class="label">' . $value . '</div>';

  echo '<div class="form"><form class="edit_form" action="javascript:true" method="GET">';
	echo '<input type="hidden" name="id" value="' . $id . '" />';
	if ($newlines > 0) {
		echo '<textarea class="noHotkeys" rows="' . ($newlines + 1) . '" name="' . $value . '">' . "\n" . $val . '</textarea>';
	} else {
		echo '<input class="noHotkeys" type="' . $row . '" name="' . $value . '" value="' . $val . '" />';
	}
	echo '<input class="submit noHotkeys" type="submit" value="Update" />';
	echo '</form></div>';
	echo '</div>';
}

$editableRows = array('num','first','last','pos','height','weight','year','hometown','stype','s1','s2','s3','s4','s5','s6','s7','s8');

echo '<div id="editTitle">';

echo "<h3>Player Data</h3>";
foreach ($editableRows as $text) {
	printEditableRow($titleId, $titleRow, $text);
}

echo '</div>'
?><br style="clear:both" />

<button tid="<?= $titleId ?>" id="render" name="Render">Force Render</button>
<button tid="<?= $titleId ?>" id="updateFields" name="UpdateFields">Update All</button>

<script type="text/javascript">
	$(".edit_form").change( function() { // keep track of changed values
    $(this).data("changed", true);
  });

  $(".edit_form").submit(function() {
    $(this).data("changed", false); // now matches the database
		var form = $(this);
		form.children("input:last").attr("value", "Submitting");
    $.ajax({
			type: "POST",
			url: "cdb_update_p.php",
			data: $(this).serializeArray(),
			success: function(data) {
				form.children("input:last").attr("value", data);
        window.renderQueue.addToQueue(<?= $titleId ?>);
			}
		});
    return false;
	});

  $("#updateFields").click(function() { // Update All
    var updated = 0;
    $(".edit_form").each(function() {
      if ($(this).data("changed")) {
        $(this).data("changed", false); // now matches the database
        updated = 1;
        var form = $(this);
		    form.children("input:last").attr("value", "Submitting");
        $.ajax({
			    type: "POST",
			    url: "cdb_update_p.php",
			    data: $(this).serializeArray(),
			    success: function(data) {
				    form.children("input:last").attr("value", data);
  			  }
		    });
      }
      if (updated) {
        window.renderQueue.addToQueue(<?= $titleId ?>);
      }
    });
  });

	$("#render").click(function() { // Force Render
    var button = $(this).html("Rendering");
    var renderTid = $(this).attr("tid");
    $.ajax({
      type: "GET",
      url: "im_render_title.php?player="+renderTid+"&bustCache=true",
      success: function(data) {
        button.html("Done Rendering");
        window.renderQueue.removeFromQueue(renderTid);
      }
    });
	});
</script>
