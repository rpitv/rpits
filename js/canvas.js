$(function() {
	$( ".selected" ).draggable({
		containment: "#canvas",
		stop: function()
		{
			var p = $(this).position();
			var name = $(this).attr("id");
			var url = "im_gui.php?update=pos&id="+title_id+"&eventId="+eventId+"&name="+name+"&x="+p.left+"&y="+p.top;
			$.ajax({
				url: url,
				succes: function(data){
					alert("Updated " + url + ", response " + data);
				}
			});
		}
	});
});
$(function() {
	$( "#slider" ).slider({
		value: 50,
		change: function(event, ui) {
			var pos = $(this).slider( "option", "value" )
			$("#zlevel").html(pos);
			$("#canvas").css("zoom",pos/100);
		}
	});
});
$(function() {
	$( "#bg-slider" ).slider({
		value: 50,
		change: function(event, ui) {
			var pos = $(this).slider( "option", "value" )
			$("#bglevel").html(pos);
			var sel = $.Color($(".selected"),"background-color");
			var selbo = $.Color($(".selected"),"border-color");
			var bg = $.Color($(".geo"),"background-color");
			var border = $.Color($(".geo"),"border-color");
			sel = sel.alpha(pos/100);
			selbo = selbo.alpha(pos/100);
			bg = bg.alpha(pos/100);
			border = border.alpha(pos/100);
			$(".geo").css("background-color",bg)
			$(".geo").css("border-color",border)
			$(".selected").css("background-color",sel)
			$(".selected").css("border-color",selbo)
		}
	});
});
function edit()
{
	var name = $(this).attr("id");
	$(".selected" ).draggable({ disabled: true });
	$(".selected" ).resizable({ disabled: true });
	var color = $.Color($(".selected"),"background-color");
	color = color.rgba(128,255,128);
	$(".selected").css("background-color",color)
	$(".layer").removeClass("layersel");
	$(".geo").removeClass("selected");


	$("#info-target").load("im_gui.php?load=attrs&id="+title_id+"&eventId="+eventId+"&name="+name)
	$(this).addClass("selected");
	$("#l-"+name).addClass("layersel");

	var color = $.Color($(this),"background-color");
	color = color.rgba(255,255,0);
	$(this).css("background-color",color)

	$(this).draggable({
		containment: "#canvas",
		disabled: false,
		stop: function()
		{
			var p = $(this).position();
			var name = $(this).attr("id");
			$("#info-x").val(p.left);
			$("#info-y").val(p.top);
			var url = "im_gui.php?update=pos&id="+title_id+"&eventId="+eventId+"name="+name+"&x="+p.left+"&y="+p.top;
			$.ajax({
				url: url,
				success: function(data){

				}
			});
		}
	});
	$(this).resizable({
		handles: "n, e, s, w",
		disabled: false,
		stop: function()
		{
			var name = $(this).attr("id");
			$("#info-w").val($(this).width());
			$("#info-h").val($(this).height());
			var p = $(this).position();
			$("#info-x").val(p.left);
			$("#info-y").val(p.top);
			var url = "im_gui.php?update=size&id="+title_id+"&eventId="+eventId+"&name="+name+"&w="+$(this).width()+"&h="+$(this).height()+"&x="+p.left+"&y="+p.top;
			$.ajax({
				url: url,
				success: function(data){
					var src = $(".selected").children("img").first().attr("src");
					$(".selected").children().first().attr("src",src+"&rand="+Math.random());

				}
			});
		}
	});
}
$(function() {
	$(".geo").click(edit);
});
$(function() {
	$("#layer-panel").sortable({
		placeholder: "ui-state-highlight",
		helper : 'clone',
		distance: 40,
		stop: function() {
			var order = 0;
			$("#layer-panel").children().each(function() {
				var name = $(this).children("h3").html();
				$("#"+name).css('z-index',order);
				order++;
			})
		}
	});
	$(".layer").click(function(){
		var name = $(this).children("h3").html();
		$("#"+name).trigger('click');
	})
});