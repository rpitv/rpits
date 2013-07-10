var ui = {};

var RPITS = {};

RPITS.ui = {};

RPITS.constants = {
	KEYCODE: {
		SPACEBAR: 32,
		LETTER_C: 67,
		ENTER: 13,
		LETTER_R: 82,
		LETTER_E: 69,
		ARROW_DOWN: 40,
		ARROW_UP: 38,
		TAB: 9		
	}
};

RPITS.ui.Title = {
	id: 0,
	name: '',
	filename: '',
	name: '',
	templateName: ''
}

RPITS.ui.Monitor = function(options) {
	this.options = options;
	this.el = $('<div id="' + options.id +'"></div>');
	this.label = $('<div class="label">'+options.name+'</div>');
	this.image = $('<div class="image"></div>');
	this.el.append(this.label);
	this.el.append(this.image);
	$('body').append(this.el);
	this.title = null;
	
	this.on = function(title) {
		this.title = title;
		this.el.addClass('on');
		this.label.text(this.options.name + ' - ' + title.name);
		var image = $('<img src="out/'+title.filename+'">');
		image.width(this.el.css('width'));
		this.image.html(image);
	};
	this.active = function() {
		return this.title;
	};
	this.off = function() {
		this.title = null;
		this.el.removeClass('on');
		this.label.text(this.options.name);
		this.image.empty();
	};
};

RPITS.ui.Console = function() {
	this.el = $('<div id="log"></div>');
	$('body').append(this.el);
	
	this.log = function(string) {
		console.log(string);
		this.el.append(string);
		this.el.animate({
			scrollTop: this.el.prop("scrollHeight") - this.el.height()
		}, 200);
	};
};

RPITS.Keyer = function(options) {
	var DURATION_IN = 15;
	var DURATION_OUT = 15;
	options = options || {};
	this.base = options.base || 'putter.php';
	this.el = $('<div id="loadTarget"></div>');
	
	this.doXHR = function(url,callback) {
		$.get(url,function(data) {
			console.log(data);
			if(typeof callback === 'function') {
				callback(data);
			}
		});
	};
	
	this.command = function(command,callback) {
		this.doXHR(this.base + '?command=' + command,callback);
	};
	
	this.put = function(title,callback) {
		this.doXHR(this.base + '?path=out/' + title.filename,callback);
	};
	
	this.offProgram = function(duration) {
		this.title = null;
		duration = duration || (this.title && this.title.durationOut) || DURATION_OUT;
		this.command('dissolve_out/' + duration);
	}
	
	this.onProgram = function(title,duration) {
		this.title = title;
		duration = duration || title.durationIn || DURATION_IN;
		this.put(title,function() {
			this.command('dissolve_in' + duration);
		}.bind(this));
	};
};

$.extend($.expr[":"], {
	"containsC": function(elem, i, match, array) {
		return (elem.textContent || elem.innerText || "").toLowerCase().indexOf((match[3] || "").toLowerCase()) >= 0;
	}
});

ui.titleObjectShim = function(el) {
	var title = {};
	title.name = el.text();
	title.filename = el.find('img').attr('path').substr(4);
	title.type = el.attr('type');
	title.id = el.attr('id');
	
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
			}	else {
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
	
	// Disabled this as it caused confusion with arrow-key movement
	//$("li").live("mouseover",function(){
	//  $(".selected").removeClass("selected");
	//  $(this).addClass("selected");
	//});
        
	// Double Click to take something on/off of program
	$("li").live("dblclick",function() {
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
	});
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