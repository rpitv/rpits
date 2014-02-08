<?php

include ("include.php");

$team_sel = $_GET["team_sel"];

if($team_sel)
{
?>

<script src="js/lib/jquery-1.5.1.min.js" type="text/javascript"></script>
<script src="js/lib/jquery-ui-1.8.12.custom.min.js" type="text/javascript"></script>
<script src="im.js" type="text/javascript"></script>
<script type="text/javascript">

$(function() {

	var eventsTable = new EditableTable({
		db: '<?= $mysql_database_name ?>',
		dbTable: 'players',
		columnHeaders: ['ID','Num','First','Last','Pos','Height','Weight','Year','Hometown','SType','S1','S2','S3','S4','S5','S6','S7','S8','Team'],
		uneditableColumns: ['id'],
		element: $('body'),
		displayFunction: {
			id: function(id) {
				return $('<a href="im_render_title.php?player='+id+'">'+id+'</a>');
			}
		}
	});
	eventsTable.loadTable(0,100,'team = "<?= $team_sel ?>"','NUM ASC');
	
	$('#all').click(function(){
		$('.erow').trigger('click');
	});

});


</script>
<style type="text/css">
	tr {
		height:30px;
	}
	tr.erow td, tr.nrow td {
		width:100px;
		max-width:150px;
		text-overflow: ellipsis;
		white-space: nowrap;
		overflow:hidden;
	}
	td.editing input {
		width: inherit;
	}
	td.action, th.action {
		width:120px;
	}
	th {
		text-align: left;
	}
	.s1, .s2, .s3, .s4, .s5, .s6, .s7, .num, .id, .pos, .height, .weight, .year, .stype {
		width:40px !important;
	}
	.hometown, .s8 {
		width:150px !important;
	}
</style>
<!--<button id="all">Edit All</button> If Matt asks for this, I'll add it, otherwise I don't like it -->
<? } else { ?>
  <h2>Select a team</h2>
  <form action="im_peditor.php" method="get">
	<?
	$query = "SELECT * FROM statscard_teams";
	$result = mysql_query($query) or die("<b>YOU DID SOMETHING WRONG BECAUSE REILLY PROBABLY CODED THIS PART POORLY</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
	while($row = mysql_fetch_array($result)) {
	  echo("<div style=\"float:left;width:100px\"><img width=\"30\" src=\"teamlogos/" . $row["logo"] . "\"><br><input type=\"submit\" name=\"team_sel\" value=\"" . $row["name"] . "\"></div>");
	} ?>
 </form>
 <? } ?>
