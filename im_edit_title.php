<link rel="stylesheet" type="text/css" href="im_edit.css" media="screen" />
<?php
include("include.php");

$titleId = $_GET["id"];
$eventId = $_GET["eventId"];

function printEditableRow($row, $id, $type, $prop = false) {
	$val = $row[$type];
	$val = htmlentities(str_replace('\n', PHP_EOL, $val));
	$newlines = substr_count($val, PHP_EOL);
	
	$name = $row["name"];
	echo '<div class="row">';
	if ($prop) {
		echo '<div class="label">' . $prop . '</div>';
	} else {
		echo '<div class="label">' . $name . '</div>';
	}

	echo '<div class="form"><form class="edit_form" action="javascript:true" method="GET">';
	echo '<input type="hidden" name="' . $id . '" value="' . $name . '" />';
	if ($newlines > 0) {
		echo '<textarea class="noHotkeys" rows="' . ($newlines + 1) . '" name="' . $type . '">' . "\n" . $val . '</textarea>';
	} else if ($type == 'color') {
		echo '<input class="noHotkeys" type="text" name="' . $type . '" value="' . $val . '" /><span class="startPicker">&#xe01e;</span>';
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

if ($geos['shadowText']) {
	echo "<div id=\"shadowText\"><h3>Shadow Text</h3>";
	foreach ($geos['shadowText'] as $geo) {
		printEditableRow($geo, $titleId, 'text');
	}
	echo "</div>";
}

if ($geos['plainText']) {
	echo "<div id=\"plainText\"><h3>Normal Text</h3>";
	foreach ($geos['plainText'] as $geo) {
		printEditableRow($geo, $titleId, 'text');
	}
	echo "</div>";
}

if ($geos['slantRectangle']) {
	echo "<div id=\"colorBars\"><h3>Color Bars</h3>";
	foreach ($geos['slantRectangle'] as $geo) {
		printEditableRow($geo, $titleId, 'color');
	}
	echo "</div>";
}

if ($geos['placeImage']) {
	echo "<div id=\"images\"><h3>Images</h3>";
	foreach ($geos['placeImage'] as $geo) {
		printEditableRow($geo, $titleId, 'path');
	}
	echo "</div>";
}

if ($geos['divingStandings']) {
	$ignore = array(' ','y','w','h','name','order','type','x');
	echo "<h3>Diving Standings</h3>";
	foreach ($geos['divingStandings'] as $geo) {
		foreach ($geo as $key=>$prop) {
			if (!array_search($key,$ignore)) {
				printEditableRow($geo,$titleId,$key,$key);
			}
		}
	}
}
if ($geos['flexBox']) {
	$ignore = array(' ','y','w','h','name','order','type','x');
	echo "<h3>Flex Box</h3>";
	foreach ($geos['flexBox'] as $geo) {
		foreach ($geo as $key=>$prop) {
			if (!array_search($key,$ignore)) {
				printEditableRow($geo,$titleId,$key,$key);
			}
		}
	}
}

if ($geos['weather']) {
	$ignore = array(' ','y','w','h','name','order','type','x', 'logoHeight', 'logoLeft', 'lineHeight', 'boxHeight', 'boxWidth', 'boxOffset', 'boxPadding', 'titleHeight', 'titleText', 'titleGravity', 'subTitleHeight', 'subTitleWidth', 'subTitleText', 'logoRight' );
	echo "<h3>Weather Graphic</h3>";
	foreach ($geos['weather'] as $geo) {
		foreach ($geo as $key=>$prop) {
			if (!array_search($key,$ignore)) {
				printEditableRow($geo,$titleId,$key,$key);
			}
		}
	}
}

if ($geos['gameSummary']) {
	$ignore = array(' ','y','w','h','name','order','type','x');
	echo "<h3>Game Summary</h3>";
	foreach ($geos['gameSummary'] as $geo) {
		foreach ($geo as $key=>$prop) {
			if (!array_search($key,$ignore)) {
				printEditableRow($geo,$titleId,$key,$key);
			}
		}
	}
}

echo '</div>'
?>
<br style="clear:both" />

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
			url: "cdb_update.php",
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

	$(document).ready(function() {
		$(".row .label:contains('Color'), .row .label:contains('color')").each( function() {
			var temp1 = $(this).next().children().children().last();
			$('<span class="startPicker">&#xe01e;</span>').insertBefore( temp1 );
		});

		$(".startPicker").each(function() {
			$(this).bind( "click", function() {
				var box = $(this).prev();
				box.addClass('color');
				jscolor.init();
				box.focus();
				$(this).hide();
			});
		});
	});
</script>
