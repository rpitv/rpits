<script src="js/jquery-1.5.1.min.js" type="text/javascript"></script>
<script src="js/jquery-ui-1.8.12.custom.min.js" type="text/javascript"></script>
<script src="im.js" type="text/javascript"></script>
<script type="text/javascript">

$(function() {

	var eventsTable = new EditableTable({
		db: 'rpihockey',
		dbTable: 'teams',
		//columnHeaders: ['ID','Name','Team 1','Team 2'],
		uneditableColumns: ['id'],
		hideColumns: ['hidden','start','end','logor','logob','logog'],
		element: $('#teamsList')
	});
	eventsTable.loadTable(0,30,"hidden='0'");

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