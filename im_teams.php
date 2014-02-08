<?php include("init.php"); ?>

<script src="js/lib/jquery-1.5.1.min.js" type="text/javascript"></script>
<script src="js/lib/jquery-ui-1.8.12.custom.min.js" type="text/javascript"></script>
<script src="im.js" type="text/javascript"></script>
<script type="text/javascript">

$(function() {

	var eventsTable = new EditableTable({
		db: '<?= $mysql_database_name ?>',
		dbTable: 'teams',
		uneditableColumns: ['id'],
		element: $('#teamsList'),
		displayFunction: {
			chn_id: function(id) {
				if(id != 0) {
					return $('<a href="statsloader.php?tid='+id+'">'+id+'</a>');
				} else {
					return 'N/A';
				}
			},
			chs_abbrev: function(chs) {
				if(chs) {
					return $('<a href="http://www.collegehockeystats.net/1314/teamstats/'+chs+'">'+chs+'</a>');
				} else {
					return '';
				}
			}
		}
	});
	eventsTable.loadTable(0,30);

});


</script>
<style type="text/css">
	tr {
		height:30px;
	}
	tr td {
		width:100px;
	}
	tr.editing input, tr.nrow input {
		width:100px;
	}
	th {
		text-align: left;
	}
	tr.erow td.action {
		width:150px;
	}
</style>
<div id="teamsList"></div>