<link rel="stylesheet" type="text/css" href="im_edit.css" media="screen" />
<?php
include("include.php");

$titleId = $_GET["id"];

$titleResult = dbquery("SELECT * from players where id=\"$titleId\" LIMIT 1;");
$titleRow = rpits_db_fetch_array($titleResult);

$stype = $titleRow["stype"];
if ($stype != "txt") {
	$titleResult = dbquery("SELECT * FROM stattype WHERE `type` = '$stype'");
	$slabel = rpits_db_fetch_array($titleResult);
}

function printEditableRow($id, $row, $value, $slabel) {
	$val = $row[$value];
	$val = str_replace('\n', PHP_EOL, $val);
	$newlines = substr_count($val, PHP_EOL);

	echo '<div class="row">';

	// replace s1-s8 with their informative labels
	if (preg_match( '/^s[1-8]$/', $value) and $slabel[$value[1]+1]) {
		$label_value = $value . ' (' . $slabel[$value[1]+1] . ')';
	} else if (preg_match( '/^s[1-8]$/', $value)) {
		$label_value = $value . ' (hidden)'; // stats w/o label aren't rendered
	} else {
		$label_value = $value;
	}

	echo '<div class="label">' . $label_value . '</div>';

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
	printEditableRow($titleId, $titleRow, $text, $slabel);
}

echo '</div>'
?>
<br style="clear:both" />

<button tid="<?= $titleId ?>" id="forceRender" name="ForceRender">Force Render</button>
<button tid="<?= $titleId ?>" id="revertChanges" name="RevertChanges">Revert Changes</button>


<script>
	$(".edit_form .noHotKeys").focus( function() {
		if (!$(this).data('initial')) {
			$(this).data("initial", $(this).val());
		}
	});

	$(".edit_form").change( function() { // submit on changed values
		$(this).data("changed", true);
		$(this).submit();
	});

	$(".edit_form").submit(function() {
		var form = $(this);
		form.children("input:last").attr("value", "Submitting");
		$.ajax({
			type: "POST",
			url: "cdb_update_p.php",
			data: $(this).serializeArray(),
			success: function(data) {
				$(this).data("changed", false); // now matches the database
				form.children("input:last").attr("value", data);
				window.renderQueue.addToQueue(<?= $titleId ?>);
			}
		});
		return false;
	});

	function updateAll(deferred) {
		var updated = 0;
		var requests = [];
		$(".edit_form").each(function() {
			if ($(this).data("changed")) {
				updated = 1;
				var form = $(this);
				form.children("input:last").attr("value", "Submitting");
				requests.push($.ajax({
					type: "POST",
					url: "cdb_update_p.php",
					data: $(this).serializeArray(),
					success: function(data) {
						$(this).data("changed", false); // now matches the database
						form.children("input:last").attr("value", data);
					}
				}));
			}
		});
		$.when(requests).then(function() {
			deferred.resolve();
		});
	}

	$("#revertChanges").click(function() { // Revert Changes Button
		var deferred = $.Deferred();
		$('.edit_form input[type=Array]').each(function() {
			if ($(this).data('initial') || $(this).data('initial')==='') {
				if ($(this).val() !== $(this).data('initial')) { 
					$(this).val($(this).data('initial'));
					$(this).parent().data('changed', true);
					console.log('Reset value to '+$(this).val());
				} else {
					console.log('No change to revert.');
				}
			}
		});
		updateAll(deferred);
		deferred.done(function() {
			// We don't actually need to queue it do we?
			//window.renderQueue.addToQueue(<?= $titleId ?>);
		});
	});

	$("#forceRender").click(function() { // Force Render Button
		var button = $(this);
		var renderTid = $(this).attr("tid");
		var deferred = $.Deferred();
		updateAll(deferred);
		deferred.done(function() {
			button.html("Rendering");
			$.ajax({
				type: "GET",
				url: "im_render_title.php?player="+renderTid+"&bustCache=true",
				success: function(data) {
					button.html("Done Rendering");
					window.renderQueue.removeFromQueue(renderTid);
				}
			});
		});
	});

</script>
