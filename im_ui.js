var ui = {};

$.extend($.expr[":"], {
	"containsC": function(elem, i, match, array) {
		return (elem.textContent || elem.innerText || "").toLowerCase().indexOf((match[3] || "").toLowerCase()) >= 0;
	}
});
$(document).keydown(function(event)
{
	if(event.keyCode != 13 && $(document.activeElement).filter('#input input').length == 1)
	{
		return;
	}
	if ($(document.activeElement).hasClass("noHotkeys") || $(document.activeElement).hasClass("noHotkeys") )	{
		return;
	}
	//alert(event.keyCode);
	// Spacebar, takes on/off of program
	if (event.keyCode == '32')
	{
		event.preventDefault();
		if($("#program").hasClass("on"))
		{
			$(".on-program").removeClass("on-program");
			$("#program .label").text("Program");
			$("#program .image").html("");
			$("#program").removeClass("on");
			$("#loadtarget").load('putter.php?command=dissolve_out/15',function()
			{
				$("#log").append($("#loadtarget").html());
				$("#log").animate({
					scrollTop: $("#log").attr("scrollHeight") - $('#log').height()
				}, 200);
			});

		}
		else
		{
			$(".selected").addClass("on-program");
			$("#program").addClass("on");
			$("#program .label").text("Program - " + $(".selected").text());
			$("#program .image").html($("<img src=\"" + $(".selected").children().first().attr("path")+'?'+Math.random() + "\" />").width(ui.viewerWidth));
			$("#loadtarget").load('putter.php?path='+$(".selected").children().first().attr("path"),function()
			{
				$("#log").append($("#loadtarget").html());
				$("#log").animate({
					scrollTop: $("#log").attr("scrollHeight") - $('#log').height()
				}, 200);
				$("#loadtarget").load('putter.php?command=dissolve_in/15',function()
				{
					$("#log").append($("#loadtarget").html());
					$("#log").animate({
						scrollTop: $("#log").attr("scrollHeight") - $('#log').height()
					}, 200);
				});

			//if($(".on-program").attr("id") == 10)
			//  $("#options").load('putter.php?command=dirty_level/1');
			//else
			//  $("#options").load('putter.php?command=dirty_level/0');
			});
		}
	}
	// c, cuts without taking down existing graphic.
	else if (event.keyCode == '67')
	{
		$(".on-program").removeClass("on-program");
		$(".selected").addClass("on-program");
		$("#program").addClass("on");
		$("#program .label").text("Program - " + $(".selected").text());
		$("#program .image").html($("<img src=\"" + $(".selected").children().first().attr("path")+'?'+Math.random() + "\" />").width(ui.viewerWidth));
		$("#loadtarget").load('putter.php?path='+$(".selected").children().first().attr("path"),function()
		{
			$("#log").append($("#loadtarget").html());
			$("#log").animate({
				scrollTop: $("#log").attr("scrollHeight") - $('#log').height()
			}, 200);
		});
	}

	// Enter, pops up search/input window
	else if(event.keyCode == '13') // Enter
	{
		event.preventDefault();
		if($("#input").is(":visible"))
		{
			$("#input input").blur();
			$("#input").hide();
			var data = $("#input input").val();
			$("li").removeClass("selected");
			var target = $("li:visible:containsC("+data+"):first");
			target.addClass("selected");
			target.scrollintoview({
				duration: 0
			});
			$("li").removeClass("on-edit");
			$("li").removeClass("on-preview");
			target.addClass("on-preview");
			$("#preview .label").text("Preview - " + target.text());
			//$("#preview .edit_target").html("");
			$("#edit").hide();
			$("#preview .image").html($("<img src=\"" + target.children().first().attr("path")+'?'+Math.random() + "\" />").width(ui.viewerWidth));
		}
		else
		{
			$("#input").show();
			$("#input input").focus();
			$("#input input").val("");
		}
	}
	// R key, for previewing
	else if(event.keyCode == '82')
	{
		$("li").removeClass("on-edit");
		$("li").removeClass("on-preview");
		$(".selected").addClass("on-preview");
		$("#preview .label").text("Preview - " + $(".selected").text());
		//$("#preview .edit_target").html("");
		$("#edit").hide();
		$("#preview .image").html($("<img src=\"" + $(".selected").children().first().attr("path")+'?'+Math.random() + "\" />").width(ui.viewerWidth));
	}
    
	// E key, for editing
	else if(event.keyCode == '69')
	{
		$("li").removeClass("on-preview");
		$("li").removeClass("on-edit");
		$(".selected").addClass("on-edit");
		$("#preview .label").text("Editing " + $(".selected").text());
		if($(".selected").attr("type")=="general")
		{
			//$("#preview .image").html("");
			$("#edit").load("im_edit_title.php?id=" + $(".selected").attr("id"),function()
			{
				$("#edit").show();
			});
          
          
		}
		else
		{
			$("#edit").hide();
			$("#preview .image").html($("<img src=\"" + $(".selected").children().first().attr("path")+'?'+Math.random() + "\" />").width(ui.viewerWidth));
		}
	}
    
	// Down arrow key
	else if(event.keyCode == '40')
	{
		event.preventDefault();
		if($(".selected + li").length > 0)
		{
			$(".selected + li").addClass("selected");
			$(".selected:first").removeClass("selected");
			$(".selected").scrollintoview({
				duration: 0
			});
		}
	}
    
	// Up arrow key
	else if(event.keyCode == '38')
	{
		event.preventDefault();
		if($(".selected").prev("li").length > 0)
		{
			$(".selected").prev("li").addClass("selected");
			$(".selected:last").removeClass("selected");
			$(".selected").scrollintoview({
				duration: 0
			});       
		}
	}
    
	// Tab key -- cycles to next tab
	else if (event.keyCode == '9')
	{
		event.preventDefault();
		if($(".active + .tab").length == 0) // if we're at the end of the tab row
		{
			$(".tab").removeClass("active");
			$(".tab:first").addClass("active");
        
		}
		else // otherwise
		{
			$(".active + .tab").addClass("active");
			$(".tab.active:first").removeClass("active");
		}
		$('.titles').removeClass("active");
		$('.titles').hide();
		$('.titles[request|="'+$(".tab.active").attr("request")+'"]').show();
		$('.titles[request|="'+$(".tab.active").attr("request")+'"]').addClass("active");
		$("li").removeClass("selected");
		$(".titles.active li:first").addClass("selected");
		$(".selected").scrollintoview({
			duration: 0
		});
	}
});
$(document).ready(function(){
	resizeWindow();
	$(".titles").each(function(){
		$(this).load($(this).attr("request"),function(){
			$(this).hide();
			$(".active").show();
			$(".titles.active li:first").addClass("selected");
		});
		$(".active").show();
		$(".titles.active li:first").addClass("selected");
	});
	$(".tab").click(function()
	{
		if($(this).hasClass("active"))
			return;
		else
		{
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
	$("li").live("dblclick",function()
	{
		if($(this).hasClass("on-program"))
		{
			$("li").removeClass("on-program");
			$("#program .label").text("Program");
			$("#program .image").html("");
			$("#program").removeClass("on");
			$("#loadtarget").load('putter.php?command=dissolve_out/15',function()
			{
				$("#log").append($("#loadtarget").html());
				$("#log").animate({
					scrollTop: $("#log").attr("scrollHeight") - $('#log').height()
				}, 200);
			});
		}
		else
		{
			$("li").removeClass("on-program");
			$(this).addClass("on-program");
			$("#program .label").text("Program - " + $(this).text());
			$("#program .image").html($("<img src=\"" + $(this).children().first().attr("path")+'?'+Math.random() + "\" />").width(ui.viewerWidth));
			$("#program").addClass("on");
			$("#loadtarget").load('putter.php?path='+$(".selected").children().first().attr("path"),function()
			{
				$("#log").append($("#loadtarget").html());
				$("#log").animate({
					scrollTop: $("#log").attr("scrollHeight") - $('#log').height()
				}, 200);
				$("#loadtarget").load('putter.php?command=dissolve_in/15',function()
				{
					$("#log").append($("#loadtarget").html());
					$("#log").animate({
						scrollTop: $("#log").attr("scrollHeight") - $('#log').height()
					}, 200);
				});
			});
		// send request to putter.php?src=$(this).children().first().attr("src")
		}

	});
	$("li").live("click",function(){
		$(".selected").removeClass("selected");
		$(this).addClass("selected");
		$("li").removeClass("on-preview");
		$("li").removeClass("on-edit");
		$(this).addClass("on-preview");
		$("#preview .label").text("Preview - " + $(this).text());
		$("#edit").hide();
		$("#preview .image").html($("<img src=\"" + $(this).children().first().attr("path")+'?'+Math.random() + "\" />").width(ui.viewerWidth));
	});
	$('#editEvents').live('click',function()
	{
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