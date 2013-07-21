<script src="js/lib/jquery-1.5.1.min.js" type="text/javascript"></script>
<script src="js/lib/jquery-ui-1.8.12.custom.min.js" type="text/javascript"></script>
<script src="im.js" type="text/javascript"></script>
<script type="text/javascript">

$(function() {

	var eventsTable = new EditableTable({
		db: 'rpihockey',
		dbTable: 'stattype',
		columnHeaders: ['ID','Type','S1','S2','S3','S4','S5','S6','S7','S8','Spacing'],
		uneditableColumns: ['id'],
		element: $('body')
	});
	eventsTable.loadTable(0,30);
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
	.l1, .l2, .l3, .l4, .l5, .l6, .l7, .l8 {
		width:80px !important;
	}
	.id, .type, .spacing {
		width:40px !important;
	}
</style>