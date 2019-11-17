var includes = [
	'js/RPITS.js',
	'js/Title.js'
];

// might be better to append the includes in PHP?

for (var script in includes) {
	document.write('<script src="'+includes[script]+'"></script>');
}

var ui = {};

$.extend($.expr[":"], {
	"containsC": function(elem, i, match, array) {
		return (elem.textContent || elem.innerText || "").toLowerCase().indexOf((match[3] || "").toLowerCase()) >= 0;
	}
});

ui.applyListeners = function() {
	var ctrlPress = 0;
	var shiftPress = 0;

	$(document).keyup(function(event) {
		if (event.keyCode == '16') {
			shiftPress = 0;
		}
		if (event.keyCode == '17') {
			ctrlPress = 0;
		}
	});
	
	$(window).blur(function() {
		shiftPress = 0;
		ctrlPress = 0;
	});
	
	$(document).keydown(function(event) {
		if (event.keyCode != 13 && $(document.activeElement).filter('#input input').length == 1)	{
			return;
		}
		
		//check if shift and ctrl are down
		if (event.keyCode == '16') {
			shiftPress = 1;
		}
		if (event.keyCode == '17') {
			ctrlPress = 1;
		}
		
		//if shift or ctrl are down, abort
		if (shiftPress || ctrlPress) {
			return;
		}
		if ($(document.activeElement).hasClass("noHotkeys") || $(document.activeElement).hasClass("noHotkeys")) {
			return;
		}

		var selected = $(".selected");
		
		// Spacebar, takes on/off of program
		if (event.keyCode == RPITS.constants.KEYCODE.SPACEBAR) {
			event.preventDefault();
			if (ui.program.active()) {
				ui.program.off();
				ui.keyer.offProgram();
			} else {
				ui.program.on(selected);
				ui.keyer.onProgram(selected);
			}
		} else if (event.keyCode == RPITS.constants.KEYCODE.LETTER_T) {		// t, brings up with tie-to-source enabled
			if (!ui.program.active()) {
				ui.program.on(selected);
				ui.keyer.onProgram(selected, 'tie');
			}
		} else if (event.keyCode == RPITS.constants.KEYCODE.LETTER_C) { 	// c, cuts without taking down existing graphic.
			if (ui.program.active()) {
				if (ui.program.active() == selected.data('title')) {
					ui.program.off();
					ui.keyer.offProgram(1);
				} else {
					ui.program.on(selected);
					ui.keyer.onProgram(selected,0);
					ui.keyer.command('cut',undefined,'animator');
				}
			} else {
				ui.program.on(selected);
				ui.keyer.onProgram(selected,1);
			}
		} else if (event.keyCode == RPITS.constants.KEYCODE.LETTER_A) { 	// a, animates , starts animation over
			if (ui.program.active()) {
				ui.program.off();
				ui.keyer.offProgram(1,'keyer');
			}
			if(selected.data('title').type == 'player') {
				ui.program.on(selected,'animate');
				ui.keyer.onProgram(selected,'animate');
			} 
			else if(selected.data('title').parent == 'templates/gameSummary.xml')
			{
				ui.program.on(selected,'animate');
				ui.keyer.onProgram(selected,'animate');
			}
			else if(selected.data('title').parent == 'templates/sog_dropdown.xml')
			{
				ui.program.on(selected,'animate');
				ui.keyer.onProgram(selected,'animate');
			}
			else if(selected.data('title').parent == 'templates/home_offense_starters.xml')
			{
				ui.program.on(selected,'animate');
				ui.keyer.onProgram(selected,'animate');
			}
			else if(selected.data('title').parent == 'templates/home_defensive_starters.xml')
			{
				ui.program.on(selected,'animate');
				ui.keyer.onProgram(selected,'animate');
			}
			else if(selected.data('title').parent == 'templates/visitor_defensive_starters.xml')
			{
				ui.program.on(selected,'animate');
				ui.keyer.onProgram(selected,'animate');
			}
			else if(selected.data('title').parent == 'templates/visitor_offense_starters.xml')
			{
				ui.program.on(selected,'animate');
				ui.keyer.onProgram(selected,'animate');
			}
			else {
				console.error('Animation not supported for non-player titles at this time. Test2');
				console.error(selected.data('title').type);
			}
		} else if (event.keyCode == RPITS.constants.KEYCODE.ENTER) { // Enter, pops up search/input window
			event.preventDefault();
			if ($("#input").is(":visible")) {
				$("#input input").blur();
				$("#input").hide();
				var data = $("#input input").val();
				$("#pane li").removeClass("selected");
				var target = $("li:visible:containsC("+data.trim()+"):first");
				target.addClass("selected");
				target.scrollintoview({duration: 0});
				ui.preview.on(target);
				$("#edit").hide();
			} else {
				$("#input").show();
				$("#input input").focus();
				$("#input input").val("");
			}
		} else if (event.keyCode == RPITS.constants.KEYCODE.LETTER_R) { // R key, for previewing
			ui.preview.on(selected);
			$("#edit").hide();    
		} else if (event.keyCode == RPITS.constants.KEYCODE.LETTER_E) { // E key, for editing
			$("li").removeClass("on-preview");
			$("li").removeClass("on-edit");
			$(".selected").addClass("on-edit");
			var title = selected.data('title');
			$("#preview .label").text("Editing " + title.getDisplayName());
			$("#edit").load(title.getEditURL(), function() {
					$("#edit").show();
			});
		} else if (event.keyCode == RPITS.constants.KEYCODE.ARROW_DOWN) { // Down arrow key
			event.preventDefault();
			if ($(".selected + li").length > 0) {
				$(".selected + li").addClass("selected");
				$(".selected:first").removeClass("selected");
				$(".selected").scrollintoview({duration: 0});
			}
		} else if (event.keyCode == RPITS.constants.KEYCODE.ARROW_UP) { // Up arrow key
			event.preventDefault();
			if ($(".selected").prev("li").length > 0) {
				$(".selected").prev("li").addClass("selected");
				$(".selected:last").removeClass("selected");
				$(".selected").scrollintoview({duration: 0});       
			}
		} else if (event.keyCode == '39') { // Right key -- cycles to next tab
			event.preventDefault();
			if ($(".active.tab").next('.tab').length) { // if we're at the end of the tab row
				ui.tabs.switchLists($(".tab.active").next('.tab'));
			} else {  // otherwise
				ui.tabs.switchLists($(".tab:first"));
			}
		} else if (event.keyCode == '37') { // Left key -- cycles to prev tab, requires completely different code than right
			event.preventDefault();
			if ($(".active.tab").prev('.tab').length) {
				ui.tabs.switchLists($(".tab.active").prev('.tab'))
			} else {
				ui.tabs.switchLists($(".tab:last"));
			}
		} else if (event.keyCode == '81') { // q key renders queue
			renderQueue.processQueue(true);
		} else if (event.keyCode == '70') { // f force render			
			document.getElementById("render").click();
		} else if (event.keyCode == '85') { // u updates all
			document.getElementById("updateFields").click();
		} else if (event.keyCode == RPITS.constants.KEYCODE.LETTER_D) { //D key, for deletion of title
			//make sure user wants to do this
			if (selected.data('title').type != "player" && confirm("Are you sure you want to delete this title?")){
				var id = selected.data('title').id;
				var eventId = ui.eventId.toString();
				selected.remove();
				
				//clear preview if in use
				if (ui.preview.active()){
					ui.preview.off();
				}
				
				var sql = 'DELETE FROM event_title WHERE title="' + id + '" AND event="' + eventId + '";';
				$.getJSON('sql.php',{sql: sql, db: '<?= $mysql_database_name ?>'});
			
				$( "li" ).first().addClass("selected");
			}
		}
	});

	// Click to Preview title
	$(document).on('click','#pane li',function(e) {
		ui.preview.on($(e.currentTarget));
		$("#edit").hide();
	});
	// Click tabs to switch lists
	$(document).on('click','#tabstrip .tab',function(e) {
		ui.tabs.switchLists($(e.currentTarget));
	});

	$(document).on('click','#updateAll',function() {
		var list = $('.tab.active').data('list');
		$.getJSON(list.url + '&checkHash=true',function(data) {
			var added = false;
			var bustCache = $('#updateAllForce:checked').val();
			var type = $('.tab.active').data('type');
			for (var id in data) {
				if (!data[id] || bustCache || type == 'player') {
					added = true;
					renderQueue.addToQueue(list.getTitleById(id), bustCache);
				}
			}
			if (added) renderQueue.processQueue(true); // soft start
		});
	});

	// bug up/down controls
	$(document).on('click','#bugUp',function() {
		$('.bug').css('background', 'url(loadCurrentBug.php?n=' + Math.random()  + ') no-repeat');
		$('.bug').css('background-size', 'contain');
		$.ajax({
			url: '/bugcontrol/dissolve_in/15',
			type: 'POST',
			success: function() {
				$('.bug').fadeIn(500);
				console.log("Bug Up");
			},
			error: function() {
				console.log("Error: Unable to toggle bug up.");
			}
		});
	});
	$(document).on('click','#bugDown', function() {
		$.ajax({
			url: '/bugcontrol/dissolve_out/15',
			type: 'POST',
			success: function() {
				$('.bug').fadeOut(500);
				console.log("Bug Down");
			},
			error: function() {
				console.log("Error: Unable to toggle bug down.");
			}
		});
	});

};

$(document).ready(function() {

	$('#editEvents').on('click',function() {
		$('#eventSelector').empty();
		var eventsTable = new EditableTable({
			db: ui.dbName,
			dbTable: 'events',
			columnHeaders: ['ID','Name','Team 1','Team 2','statsLink'],
			uneditableColumns: ['id'],
			element: $('#eventSelector'),
			callback: function() {
				$('#eventSelector').append($('<button>Done</button>').on('click', function() {
					window.location = "im_ui.php";
				}));
			}
		});
		eventsTable.loadTable(0,30);
	});
	
	if (!ui.eventId) return;

	ui.program = new RPITS.ui.Monitor({name:'Program',id:'program'});
	ui.preview = new RPITS.ui.Monitor({name:'Preview',id:'preview',remove:'on-edit'});
	//ui.log = new RPITS.ui.Console();
	
	ui.keyer = new RPITS.Keyer();
	
	ui.applyListeners();

	ui.tabs = new RPITS.ui.ListTabs(ui.eventId,{dbName:ui.dbName,billboards:false});
	
	resizeWindow();	
});

$(window).resize(resizeWindow);

function resizeWindow() {
	if (!ui.lockResize) {
		if ($(window).width() >= $(window).height()*1.2) { // full-screen view
			$('#program').show();
			ui.maxLeftWidth = 800;
			ui.viewerHeight = (window.innerHeight - 75)/2;
			ui.viewerWidth = ui.viewerHeight * 16/9;
			ui.sideMargins = 25;
			ui.leftWidth = window.innerWidth - ui.viewerWidth - 75;
			if ((window.innerWidth - ui.viewerWidth - 25) > ui.maxLeftWidth) {
				ui.sideMargins = (window.innerWidth - ui.viewerWidth - 25 - ui.maxLeftWidth)/2;
				ui.leftWidth = ui.maxLeftWidth;
			}
			$('#program, #preview, #edit, #program img, #preview img').height(ui.viewerHeight).width(ui.viewerWidth).css('right',ui.sideMargins);
			$('#preview, #edit, #preview img').css({"top":"25px", "left":"auto"});
			$('#tabstrip').css({"top":"25px", "left":"25px", "padding-right":"2px"});
			$('#pane').css({"top":"75px", "right":"25px", "height":window.innerHeight-100, "border-left":"2px solid black"});
			$('#pane,#tabstrip,#log').width(ui.leftWidth).css('left',ui.sideMargins);
		} else { // half-screen view
			ui.viewerWidth = window.innerWidth-50;
			ui.viewerHeight = ui.viewerWidth * 9/16;
			$('#program').hide();
			$('#preview, #edit, #preview img').height(ui.viewerHeight).width(ui.viewerWidth).css({"top":"0", "left":"25px"});
			$('#tabstrip').css({"top":ui.viewerHeight+10, "left":"0", "width":"100%", "padding-right":"0"});
			$('#pane').css({"top":ui.viewerHeight+60, "left":"0", "width":"100%", "height":(window.innerHeight-ui.viewerHeight)-60, "border":"0"});
		}
	}
}



//handle select change to add title
$(function() {
	$('#addSelect').on('change', function (e) {
		//if it has a value (not empty), add the title
		if ($("#addSelect").val() != "+")
		{
			//prompt user for name
			var name = prompt("Name for the new title:");
			if (name != null){
				//http://graphics.ru.rpitv.org/rpits/im_event_title.php?parent=templates%2F2line.xml&name=&eventId=39&add=Add
				var url = "im_event_title.php?parent=" + $("#addSelect").val() + "&name=" + name + "&eventId=" + ui.eventId.toString() + "&add=Add";
				$.post( url, function(data){
					location.reload();
				});
			}
			
		}
	});
});
