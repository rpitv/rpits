<?php

include ("init.php");
include ("include.php");
mysql_select_db("rpits");

$eventId = $_GET["eventId"];

?>
<form action="im_event_title.php" method="GET">
	Select Event: <select name="eventId" >
		<?
		$result = dbquery('SELECT * FROM events');
		while ($row = mysql_fetch_array($result)) {
			$sel = '';
			if($eventId == $row["id"]) {
				$sel = 'selected="selected"';
			}
			echo '<option value="' . $row["id"] . '" ' . $sel . ' >' . $row["name"] . '</option>';
		}	?>
	</select>
	<input type="submit" />
	<a href="im_events.php">(edit/add events)</a>
</form>

<?
if($_GET["add"] == 'Add') {
	$name = $_GET["name"];
	$filename = $_GET["filename"];
	$templateId = $_GET["template"];
	dbquery("INSERT INTO titles (name,filename,template) VALUES ('$name','$filename','$templateId');");
	$titleId = mysql_insert_id();
	dbquery("INSERT INTO event_title (event,title) VALUES ('$eventId','$titleId');");
	$eventTitleId = mysql_insert_id();
}

if($eventId > 0) {

?>
<p><strong>Create and attach new title to this event</strong></p>
<form action="im_event_title.php" method="GET">
	Template: <select name="template" >
		<?
		$result = dbquery('SELECT * FROM templates');
		while ($row = mysql_fetch_array($result)) {
			echo '<option value="' . $row["id"] . '">' . $row["name"] . '</option>';
		}	?>
	</select>
	Name: <input type="text" name="name" />, Filename: <input type="text" name="filename">.png
	<input type="hidden" name="eventId" value="<?= $eventId ?>" />
	<input type="submit" name="add" value="Add" />
</form>

<script src="js/jquery-1.8.3.js" type="text/javascript"></script>
<style type="text/css">
	.title button {
		float:right;
		height:38px;
	}
	.title {
		width: 600px;
		line-height:38px;
		height:38px;
		border-bottom: black solid 1px;
	}
	.title img {
		vertical-align:middle;
	}
	#titleList {
		float:left;
	}
	#editTarget {
		float:left;
		margin-left:10px;
		width:600px;
		height:700px;
		border:solid 1px black;
		overflow:scroll;
	}
	#editTarget img {
		width:inherit;
	}
</style>

<script type="text/javascript">
	
var eventId = <?= $eventId ?>;

$(function() {
	$('.rename').click(function(e){
		var button = $(e.currentTarget);
		if(!button.hasClass('save')) {
			var titleName = button.siblings('.titleName').text();
			button.siblings('.titleName').replaceWith('<input type="text" value="' + titleName + '" />');
			button.addClass('save');
			button.text('Save');
		} else {
			var titleName = button.siblings('input').val();
			var id = button.parent().attr('id');
			button.siblings('input').replaceWith('<span class="titleName">' + titleName + '</span>');
			var sql = 'UPDATE titles SET name="' + titleName + '" WHERE id="' + id + '";';
			$.getJSON('sql.php',{sql: sql, db:'rpits'},function(d) {
				button.removeClass('save');
				button.text('Rename');
			});
		}
	});
	$('.delete').click(function(e) {
		var button = $(e.currentTarget);
		var id = button.parent().attr('id');
		var sql = 'DELETE FROM event_title WHERE title="' + id + '" AND event="' + eventId + '";';
		$.getJSON('sql.php',{sql: sql, db:'rpits'},function(d) {
			button.parent().remove();
		});
	})
	$('.edit').click(function(e) {
		var id = $(e.currentTarget).parent().attr('id');
		$('#editTarget').load('im_edit_title.php?id=' + id);
	});
	$('.preview').click(function(e) {
		var path = $(e.currentTarget).siblings('img').attr('path');
		$('#editTarget').empty().append('<img src="' + path + '"/>');
	})
});

</script>
<div id="titleList">
<?

$result = dbquery("SELECT *, event_title.id as etid, titles.filename as Tfilename, templates.name as template_name, titles.name as title_name, titles.id as title_id FROM event_title LEFT JOIN titles on titles.id = event_title.title JOIN templates ON titles.template = templates.id WHERE event_title.event = $eventId ORDER BY titles.id ASC");
while ($row = mysql_fetch_array($result)) {
  echo('<div id="' . $row["title_id"] . '" class="title">' .
			'<img src="thumbs/' . $row["Tfilename"] . '.png" path="out/' . $row["Tfilename"] . '.png" height="38" />' . 
					'<span class="titleName">' . $row["title_name"] . '</span> (' . $row["template_name"] . ')' .
			'<button class="delete">Delete</button><button class="rename">Rename</button>' . 
			'<button class="edit">Edit</button><button class="preview">Preview</button></div>');
}
?>
</div>
<div id="editTarget"></div>
<?
}
?>
