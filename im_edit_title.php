<link rel="stylesheet" type="text/css" href="im_edit.css" media="screen" />
<!--<script src="js/lib/jquery-1.5.1.min.js" type="text/javascript"></script>
<script src="js/lib/jquery-ui-1.8.12.custom.min.js" type="text/javascript"></script>-->
<?php
include("include.php");

$titleId = $_GET["id"];
$eventId = $_GET["eventId"];

function printEditableRow($row, $id, $type, $prop = false) {
	$val = $row[$type];
	$val = str_replace('\n', PHP_EOL, $val);
	$newlines = substr_count($val, PHP_EOL);
	$name = $row["name"];
	echo '<div class="row">';
	if($prop) {
		echo '<div class="label">' . $prop . '</div>';
	} else {
		echo '<div class="label">' . $name . '</div>';
	}
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

$title = getTitle($titleId,$eventId,false);
$geos = groupGeosByType($title['geos']);

echo '<div id="editTitle">';

if($geos['shadowText']) {
	echo "<h3>Shadow Text</h3>";
	foreach ($geos['shadowText'] as $geo) {
		printEditableRow($geo, $titleId, 'text');
	}
}

if($geos['plainText']) {
	echo "<h3>Normal Text</h3>";
	foreach ($geos['plainText'] as $geo) {
		printEditableRow($geo, $titleId, 'text');
	}
}

if($geos['slantRectangle']) {
	echo "<h3>Color Bars</h3>";
	foreach ($geos['slantRectangle'] as $geo) {
		printEditableRow($geo, $titleId, 'color');
	}
}

if($geos['placeImage']) {
	echo "<h3>Images</h3>";
	foreach ($geos['placeImage'] as $geo) {
		printEditableRow($geo, $titleId, 'path');
	}
}

if($geos['divingStandings']) {
	$ignore = array(' ','y','w','h','name','order','type','x');
	echo "<h3>Diving Standings</h3>";
	foreach($geos['divingStandings'] as $geo) {
		foreach($geo as $key=>$prop) {
			if(!array_search($key,$ignore)) {
				printEditableRow($geo,$titleId,$key,$key);
			}
		}
	}
}

echo '</div>'
?><br style="clear:both" />

<button tid="<?= $titleId ?>" id="render" name="Render">Force Render</button>
<button tid="<?= $titleId ?>" id="updateAll" name="UpdateAll">Update All</button>

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
			url: "cdb_update.php",
			data: $(this).serializeArray(),
			success: function(data) {
				form.children("input:last").attr("value", data);
        window.renderQueue.addToQueue(<?= $titleId ?>);
			}
		});
    return false;
	});
	
  $("#updateAll").click(function() { // Update All
    var updated = 0;
    $(".edit_form").each(function() {
      if ($(this).data("changed")) {
        $(this).data("changed", false); // now matches the database
        updated = 1;
        var form = $(this);
		    form.children("input:last").attr("value", "Submitting");
        $.ajax({
			    type: "POST",
			    url: "cdb_update.php",
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
      url: "im_render_title.php?id="+renderTid+"&bustCache=true" + (ui.eventId ? '&eventId=' + ui.eventId : ''),
      success: function(data) {
        button.html("Done Rendering");
        window.renderQueue.removeFromQueue(renderTid);
      }
    });
	});
</script>

