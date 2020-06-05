<?php

header('Content-Type: text/html; charset=utf-8');

include ("include.php");
// include ("chn_scraper.php");

$team_sel = $_GET["team_sel"] ?? '';

if($team_sel)
{
?>

<!--<form action="im_peditor.php" style="display:inline-block" ><input type="submit" name="stats" value="Update All Stats"/><input type="hidden" name="team_sel" value="<?= $team_sel ?>"/></form>-->

<?
if(isset($_GET['stats'])) {
	$team = fetchTeam($team_sel);
	$chn = fopen("http://www.collegehockeynews.com/stats/team-overall.php?td=" . $team['chn_id'],"r");
	$contents = stream_get_contents($chn);
	$contents = mb_convert_encoding($contents, 'UTF-8', 'ASCII');
	$contents = str_replace("\xc2\x9a", "\xc5\xa1" , $contents); // replace incorrect "Single Character Introducer" with "Small Latin S with Caron"
	$data = parser($contents);
	$output = '<table id="details">';
	foreach($data as $players) {
		$output .= update($players);
	}
	$output .= "</table>";
	echo ' - Updated, details <a href="#details">below the table</a>.<div class="playerTable"></div>' . $output;
} else {
	echo '<div class="playerTable"></div>';
}

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
		element: $('.playerTable'),
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
	$query = "SELECT * FROM teams";
	$result = dbquery($query);
	while($row = $result->fetch_array()) {
		$team = fetchTeam($row['player_abbrev']);
	  echo("<div style=\"float:left;width:100px\"><img width=\"30\" src=\"" . $team["logo"] . "\"><br><input type=\"submit\" name=\"team_sel\" value=\"" . $team["player_abbrev"] . "\"></div>");
	} ?>
 </form>
 <? } ?>
