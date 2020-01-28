<?php include("init.php"); ?>

<script src="js/lib/jquery-1.8.3.js" type="text/javascript"></script>
<script src="js/lib/jquery-ui-1.8.12.custom.min.js" type="text/javascript"></script>
<script src="im.js" type="text/javascript"></script>
<script type="text/javascript">

$(function() {
	var eventsTable = new EditableTable({
		dbTable: 'teams',
		uneditableColumns: ['id', 'chn_id'],
		element: $('#teamsList'),
		displayFunction: {
			org: function(organ) {
				return $('<a href="im_organizations.php">'+organ+'</a>');
			},
			chn_id: function(id) {
				if (id != 0) {
					return id;
				} else {
					return 'N/A';
				}
			},
			chs_abbrev: function(chs) {
				if (chs) {
					return $('<a href="addTeamCsv.php?pull_url='+chs+'&pull=Submit">'+chs+'</a>');
				} else {
					return '';
				}
			}
		}
	});
	
	eventsTable.loadTable(0,200);
	$(".chn_id").hide();

});
/*
$(document).ready( function() {

  $("#UpdateAllCHN").click( function() {
    $("#details").empty(); // get rid of old update info (if any)
    $(".erow").each( function() {
      var chn_id = $(this).find(".chn_id a").text();
      if ($.isNumeric(chn_id)) {
        var team_name = $(this).find(".player_abbrev").text();
        $("<div>").load("statsloader.php?tid="+chn_id, function() {
          $("#details").append( '<div><h3><a href="im_peditor.php?team_sel='+team_name+'">'+team_name+'</a></h3>'+$(this).html()+'</div>' );
        });
      }
    }).promise().done(function() {
      if ($("#header span").size()==0) { // only show stats message if not there
        $("#header").append('<span>Stats Updated, details <a href="#details">below the table</a>.</span>');
      }
    });
  });
});
*/
</script>
<style type="text/css">
	#teamsList table {
		margin-top: 1em;
	}
	#teamsList tr {
		height:30px;
	}
	#teamsList tr td {
		width:100px;
	}
	#teamsList tr.editing input, tr.nrow input {
		width:100px;
	}
	#teamsList th {
		text-align: left;
	}
	#teamsList r.erow td.action {
		width:150px;
	}

	#details div {
		min-width: 300px;
		min-height: 850px;
		float: left;
		clear: right;
	}
	#details h3 {
		margin-bottom: .2em;
	}
</style>
<div id="header">
  <h1>Teams</h1>
  <!--<button type="button" name="UpdateAllMen" id="UpdateAllMen">Update Men's Hockey Stats</button>-->
</div>
<div id="teamsList"></div>
<div id="details"></div>
