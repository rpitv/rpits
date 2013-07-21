<script src="js/lib/jquery-1.5.1.min.js" type="text/javascript"></script>
<script src="js/lib/jquery-ui-1.8.12.custom.min.js" type="text/javascript"></script>
<script src="im.js" type="text/javascript"></script>
<script type="text/javascript">

	$(function() {

		var eventsTable = new EditableTable({
			db: 'rpits',
			dbTable: 'events',
			columnHeaders: ['ID','Name','Team 1','Team 2'],
			uneditableColumns: ['id'],
			element: $('body')
		});
		eventsTable.loadTable(0,30);

	});


</script>
<style type="text/css">
	tr.erow {
		height:30px;
	}
	tr.erow td, tr.nrow td {
		width:100px;
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
</style>
