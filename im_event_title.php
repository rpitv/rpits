<?php

include ("include.php");

$eventId = $_GET["eventId"] ?? '';
$add = $_GET["add"] ?? '';

?>
<form action="im_event_title.php" method="GET">
	Select Event: <select name="eventId" >
		<?
		$result = dbquery('SELECT * FROM events');
		while ($row = $result->fetch_array()) {
			$sel = '';
			if ($eventId == $row["id"]) {
				$sel = 'selected="selected"';
			}
			echo '<option value="' . $row["id"] . '" ' . $sel . ' >' . $row["name"] . '</option>';
		}	?>
	</select>
	<input type="submit" />
	<a href="im_events.php">(edit/add events)</a>
</form>

<?php
if ($add == 'Add') {
	$name = $_GET["name"];
	$parentId = $_GET["parent"];
	$result = dbquery("INSERT INTO titles (name,parent) VALUES ('$name','$parentId');");
	$titleId = $mysqli->insert_id;
	$result = dbquery("INSERT INTO event_title (event,title) VALUES ('$eventId','$titleId');");
	$eventTitleId = $mysqli->insert_id;
} else if ($add == 'Attach') {
	$titleId = $_GET["titleId"];
	$eventId = $_GET["eventId"];
	dbquery("INSERT INTO event_title (event,title) VALUES('$eventId','$titleId')");
}

if ($eventId > 0) {

?>
<p><strong>Create and attach new title to this event</strong></p>
<form action="im_event_title.php" method="GET">
From an XML template: 	<select name="parent">
<?php
		$templates = glob("templates/*.xml");
		foreach ($templates as $template) {
			echo '<option value="'.$template.'">'.$template.'</option>';
		}
?>
	</select>
	Name: <input type="text" name="name" />
	<input type="hidden" name="eventId" value="<?= $eventId ?>" />
	<input type="submit" name="add" value="Add" />
</form>

<?php
/* Disabling inheriting titles for now due to confusion / potential misuse
<form action="im_event_title.php" method="GET">
	Inherit from existing title: <select name="parent" >
<?php
		$result = dbquery("SELECT * FROM titles");
		while ($row = mysql_fetch_array($result)) {
			echo '<option value="' . $row["id"] . '">' . $row["name"] . '</option>';
		}
?>
	</select>
	Name: <input type="text" name="name" />
	<input type="hidden" name="eventId" value="<?= $eventId ?>" />
	<input type="submit" name="add" value="Add" />
</form> */
?>
<p><strong>Attach existing title</strong></p>
From event: <select id="existingEvent">
<?php
	$result = dbquery("SELECT * FROM events");
	while ($row = $result->fetch_array()) {
		echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
	}
?>
</select>
<form acion="im_event_title.php" method="GET">
	<select id="existingList" name="titleId"></select>
	<input type="hidden" name="eventId" value="<?= $eventId ?>" />
	<input type="submit" name="add" value="Attach" />
</form>

<div style="clear: both"></div>

<script src="js/lib/jquery-1.8.3.js" type="text/javascript"></script>
<style type="text/css">
	.title button {
		float: right;
		height: 38px;
	}
	.title {
		width: 600px;
		line-height: 38px;
		height: 38px;
		border-bottom: black solid 1px;
	}
	.title img {
		vertical-align: middle;
	}
	#titleList {
		float: left;
	}
	#editTarget {
		float: left;
		margin-left: 10px;
		width: 600px;
		height: 700px;
		border: solid 1px black;
		overflow: scroll;
	}
	#editTarget img {
		width: inherit;
	}
	form {
		display: inline-block;
	}
</style>

<script type="text/javascript">
	
var eventId = <?= $eventId ?>;

$(function() {
	$('.rename').click(function(e){
		var button = $(e.currentTarget);
		if (!button.hasClass('save')) {
			var titleName = button.siblings('.titleName').text();
			button.siblings('.titleName').replaceWith('<input type="text" value="' + titleName + '" />');
			button.addClass('save');
			button.text('Save');
		} else {
			var titleName = button.siblings('input').val();
			var id = button.parent().attr('id');
			button.siblings('input').replaceWith('<span class="titleName">' + titleName + '</span>');
			var sql = 'UPDATE titles SET name="' + titleName + '" WHERE id="' + id + '";';
			$.getJSON('sql.php',{sql: sql, db: '<?= $mysql_database_name ?>'},function(d) {
				button.removeClass('save');
				button.text('Rename');
			});
		}
	});
	$('.delete').click(function(e) {
		var button = $(e.currentTarget);
		var id = button.parent().attr('id');
		var sql = 'DELETE FROM event_title WHERE title="' + id + '" AND event="' + eventId + '";';
		$.getJSON('sql.php',{sql: sql, db: '<?= $mysql_database_name ?>'},function(d) {
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
	});
	$('#existingEvent').on('change',function(e){
		var eventId = $(e.currentTarget).val();
		$.getJSON('im_title_list.php?event='+eventId+'&format=json',function(data) {
			$('#existingList').empty();
			for (var index in data) {
				$('#existingList').append('<option value="'+data[index]['id']+'">'+data[index]['name']+'</option>');
			}
		});
	});
	$('#existingEvent').trigger('change');
});

</script>
<div id="titleList">
<?php
$result = dbquery("SELECT *, event_title.id as etid, titles.name as title_name, titles.id as title_id FROM event_title LEFT JOIN titles on titles.id = event_title.title WHERE event_title.event = $eventId ORDER BY titles.id ASC");
while ($row = $result->fetch_array()) {
	if ($row['title_id']) {
		echo('<div id="' . $row["title_id"] . '" class="title">' .
			'<img src="thumbs/' . $row["title_name"] . $row["title_id"] . '.png" path="out/' . $row["title_name"] . $row["title_id"] . '.png" height="38" />' . 
					'<span class="titleName">' . $row["title_name"] . '</span>' .
			'<button class="delete">Delete</button><button class="rename">Rename</button>' . 
			'<button class="edit">Edit</button><button class="preview">Preview</button></div>');
	}
}
?>
</div>
<div id="editTarget"></div>
<?php
}
?>
