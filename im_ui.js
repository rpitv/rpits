var includes = [
	'js/RPITS.js',
	'js/Title.js'
];

// might be better to attempt this in PHP?

for(var script in includes) {
	document.write('<script src="'+includes[script]+'"></script>');
}

var ui = {};

$.extend($.expr[":"], {
	"containsC": function(elem, i, match, array) {
		return (elem.textContent || elem.innerText || "").toLowerCase().indexOf((match[3] || "").toLowerCase()) >= 0;
	}
});

ui.titleObjectShim = function(el) {
	var title = {};
	title.name = el.text();
	title.type = el.attr('type');
	title.id = el.attr('id');
	title.path = el.children('img').attr('path');
	title.getFilename = function() {
		if(this.type == 'general') {
			return this.name + this.id + '.png';
		} else if (this.type == 'player') {
			return this.path;
		} else {
			console.error("whatever this title type is, it isn't supported.");
		}
	};
	return title;
};

ui.applyListeners = function() {
	$(document).keydown(function(event) {
		if(event.keyCode != 13 && $(document.activeElement).filter('#input input').length == 1)	{
			return;
		}
		if ($(document.activeElement).hasClass("noHotkeys") || $(document.activeElement).hasClass("noHotkeys") )	{
			return;
		}
		
		// Spacebar, takes on/off of program
		if (event.keyCode == RPITS.constants.KEYCODE.SPACEBAR) {
			event.preventDefault();
			if(/*ui.program.active()*/ $('.on-program').length > 0)	{
				var activeEl = $(".on-program");
				var activeTitle = ui.titleObjectShim(activeEl);
				ui.program.off();
				activeEl.removeClass("on-program");
				ui.keyer.offProgram();
			}	else {
				var activeEl = $(".selected");
				activeEl.addClass("on-program");
				activeTitle = ui.titleObjectShim(activeEl);
				ui.program.on(activeTitle);
				ui.keyer.onProgram(activeTitle);
				//if($(".on-program").attr("id") == 10)
				//  $("#options").load('putter.php?command=dirty_level/1');
				//else
				//  $("#options").load('putter.php?command=dirty_level/0');
			}
		}	else if (event.keyCode == RPITS.constants.KEYCODE.LETTER_C)	{ 	// c, cuts without taking down existing graphic.
			$(".on-program").removeClass("on-program");
			var activeEl = $(".selected");
			activeEl.addClass("on-program");
			activeTitle = ui.titleObjectShim(activeEl);
			ui.program.on(activeTitle);
			ui.keyer.put(activeTitle);
		}	else if(event.keyCode == RPITS.constants.KEYCODE.ENTER) { // Enter, pops up search/input window
			event.preventDefault();
			if($("#input").is(":visible")) {
				$("#input input").blur();
				$("#input").hide();
				var data = $("#input input").val();
				$("li").removeClass("selected");
				var target = $("li:visible:containsC("+data+"):first");
				target.addClass("selected");
				target.scrollintoview({duration: 0});
				$("li").removeClass("on-edit");
				$("li").removeClass("on-preview");
				target.addClass("on-preview");
				
				ui.preview.on(ui.titleObjectShim(target));
				$("#edit").hide();
			}	else {
				$("#input").show();
				$("#input input").focus();
				$("#input input").val("");
			}
			
		}	else if(event.keyCode == RPITS.constants.KEYCODE.LETTER_R) { // R key, for previewing
			$("li").removeClass("on-edit");
			$("li").removeClass("on-preview");
			var activeEl =	$(".selected")
			activeEl.addClass("on-preview");
			ui.preview.on(ui.titleObjectShim(activeEl));
			$("#edit").hide();
    
		}	else if(event.keyCode == RPITS.constants.KEYCODE.LETTER_E) { // E key, for editing
			$("li").removeClass("on-preview");
			$("li").removeClass("on-edit");
			$(".selected").addClass("on-edit");
			$("#preview .label").text("Editing " + $(".selected").text());
			if($(".selected").attr("type") == "general") {
				$("#edit").load("im_edit_title.php?id=" + $(".selected").attr("id"), function() {
					$("#edit").show();
				});
			}	else if ($(".selected").attr("type") == "general") {
				$("#edit").load("im_edit_ptitle.php?id=" + $(".selected").attr("id"),function() {
					$("#edit").show();
				});
			} else {
				ui.keyer.onPreview(ui.titleObjectShim($(".selected")));
				$("#edit").hide();
			}
		}	else if(event.keyCode == '40') { 		// Down arrow key
			event.preventDefault();
			if($(".selected + li").length > 0) {
				$(".selected + li").addClass("selected");
				$(".selected:first").removeClass("selected");
				$(".selected").scrollintoview({duration: 0});
			}
		} else if(event.keyCode == '38') { // Up arrow key
			event.preventDefault();
			if($(".selected").prev("li").length > 0) {
				$(".selected").prev("li").addClass("selected");
				$(".selected:last").removeClass("selected");
				$(".selected").scrollintoview({duration: 0});       
			}
		}	else if (event.keyCode == '9') { // Tab key -- cycles to next tab
			event.preventDefault();
			if($(".active + .tab").length == 0) { // if we're at the end of the tab row
				$(".tab").removeClass("active");
				$(".tab:first").addClass("active");
			}	else {  // otherwise
				$(".active + .tab").addClass("active");
				$(".tab.active:first").removeClass("active");
			}
			$('.titles').removeClass("active");
			$('.titles').hide();
			$('.titles[request|="'+$(".tab.active").attr("request")+'"]').show();
			$('.titles[request|="'+$(".tab.active").attr("request")+'"]').addClass("active");
			$("li").removeClass("selected");
			$(".titles.active li:first").addClass("selected");
			$(".selected").scrollintoview({duration: 0});
		}
	});
};


$(document).ready(function() {

	ui.program = new RPITS.ui.Monitor({name:'Program',id:'program'});
	ui.preview = new RPITS.ui.Monitor({name:'Preview',id:'preview'});
	ui.log = new RPITS.ui.Console();
	
	ui.keyer = new RPITS.Keyer();
	
	ui.applyListeners();
	
	resizeWindow();
	$(".titles").each(function() {
		$(this).load($(this).attr("request"),function() {
			$(this).hide();
			$(".active").show();
			$(".titles.active li:first").addClass("selected");
		});
		$(".active").show();
		$(".titles.active li:first").addClass("selected");
	});
	$(".tab").click(function() {
		if($(this).hasClass("active")) {
			return;
		} else {
			$(".tab").removeClass("active");
			$(this).addClass("active");
			$('.titles').removeClass("active");
			$('.titles').hide();
			$('.titles[request|="'+$(this).attr("request")+'"]').show();
			$('.titles[request|="'+$(this).attr("request")+'"]').addClass("active");
			$("li").removeClass("selected");
			$(".titles.active li:first").addClass("selected");
		}
	});
	$(function() {
		$( ".titles" ).sortable({
			placeholder: "ui-state-highlight",
			helper : 'clone',
			distance:40
		});
		$( ".titles" ).disableSelection();
	});

	$('#updateAll').on('click',function() {
		$.getJSON($('.tab.active').attr('request') + '&checkHash=true',function(data) {
			var added = false;
			var bustCache = $('#updateAllForce:checked').val();
			var type = $('.tab.active').attr('type');
			for(var id in data) {
				if(!data[id] || bustCache || type == 'player') {
					added = true;
					renderQueue.addToQueue(id,bustCache);
				}
			}
			if(added) renderQueue.processQueue();
		});
	});

	
	// Disabled this as it caused confusion with arrow-key movement
	//$("li").live("mouseover",function(){
	//  $(".selected").removeClass("selected");
	//  $(this).addClass("selected");
	//});
        
	// Double Click to take something on/off of program
	//
	// Disabled by Ben due to potential for "Oh SHIT!"

	/*$("li").live("dblclick",function() {
		var activeEl = $(this);
		var activeTitle = ui.titleObjectShim(activeEl);
		$("li").removeClass("on-program");
		if(activeEl.hasClass("on-program")) {
			ui.program.off();
			ui.keyer.offProgram();
		}	else {
			activeEl.addClass("on-program");
			ui.program.on(activeTitle);
			ui.keyer.onProgram(activeTitle);
		}
	});*/
	$("li").live("click",function(){
		var activeEl = $(this);
		$("li").removeClass("on-preview on-edit selected");
		activeEl.addClass("selected on-preview");
		ui.preview.on(ui.titleObjectShim(activeEl));
		$("#edit").hide();
		document.activeElement.blur();
	});
	$('#editEvents').live('click',function() {
		$('#eventSelector').empty();
		var eventsTable = new EditableTable({
			db: 'rpits',
			dbTable: 'events',
			columnHeaders: ['ID','Name','Team 1','Team 2'],
			uneditableColumns: ['id'],
			element: $('#eventSelector'),
			callback: function() {
				console.log("callback fired");
				$('#eventSelector').append($('<button>Done</button>').on('click', function(){
					console.log('wtf');
					window.location = "im_ui.php";
				}));
			}
		});
		eventsTable.loadTable(0,30);
	});
});

$(window).resize(resizeWindow);

function resizeWindow() {
	if(!ui.lockResize) {
		ui.maxLeftWidth = 800;
		ui.viewerHeight = (window.innerHeight - 75)/2;
		ui.viewerWidth = ui.viewerHeight * 16/9
		ui.sideMargins = 25;
		ui.leftWidth = window.innerWidth - ui.viewerWidth - 75;
		if((window.innerWidth - ui.viewerWidth - 25) > ui.maxLeftWidth) {
			ui.sideMargins = (window.innerWidth - ui.viewerWidth - 25 - ui.maxLeftWidth)/2;
			ui.leftWidth = ui.maxLeftWidth;
		}	
		$('#program,#preview,#edit,#program img,#preview img').height(ui.viewerHeight).width(ui.viewerWidth).css('right',ui.sideMargins);
		$('#pane').height(window.innerHeight - 250);
		$('#pane,#tabstrip,#log').width(ui.leftWidth).css('left',ui.sideMargins);
	}
}
