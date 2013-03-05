<?php
include("include.php");
include("imagick_include.php");

$id = $_GET["id"];
?>
<!--<link rel="stylesheet" href="http://static.jquery.com/ui/css/demo-docs-theme/ui.theme.css" type="text/css" media="all">-->
<link rel="stylesheet" href="http://code.jquery.com/ui/1.8.21/themes/base/jquery-ui.css" type="text/css" media="all">
<script type="text/javascript" src="js/jquery-1.5.1.min.js"></script>
<script type="text/javascript" src="js/jquery.color-2.0b1.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.12.custom.min.js"></script>
<style type="text/css">
  #ccontainer
  {
    background-color:#888888;
    width:980px;
    height:560px;
    padding:0px;
    margin:0px;
    /*display: table-cell;*/
    vertical-align:middle;
    text-align:center;
    overflow:scroll;
    float:left;
  }
  #info-panel
  {
    padding-left:5px;
    float:left;
    height:560px;
    margin-left:20px;
    background-color:white;
    width:300px;
  }
  #layer-panel
  {
    padding-left:5px;
    float:left;
    height:560px;
    margin-left:20px;
    background-color:white;
    width:300px;
  }
  #canvas
  {
    padding:0px;
    margin:auto auto;
    width:1920px;
    height:1080px;
    background-color:white;
    position:relative;
    overflow:hidden;
    display:inline-block;
  }
  body
  {
    background-color:#AAAAAA;
  }
  .geo
  {
    background-color:rgba(128,255,128,0.5);
    border-style:solid;
    border-width:1px;
    border-color:rgba(0,0,0,1);
    /*background-position: -10px,-10px;*/
    overflow:visible;
    opacity:1;
  }
  .geo img
  {
    pointer-events:none;
    position:relative;
    top:-10px;
    left:-10px;
  }
  .slide
  {
    width:200px;
    height:10px;
    background-color:white;
    margin-left:20px;
    margin-bottom:10px;
    margin-top:30px;
    display:inline-block;
    vertical-align:text-bottom;
  }
  .ui-slider-handle
  {
    width:16px;
    height:16px;
    top:-3px;
    margin-left:-8px;
    background-color:grey;
    display:block;
    position:relative;
    border:black solid 1px;
  }
  .selected
  {
    border-color:red;
    background-color:rgba(255,255,0,0.5);
  }
  .updated
  {
    border:green solid 2px;
  }
  .outdated
  {
    border:red solid 2px;
  }
  .layersel
  {
    background-color:yellow;
  }

</style>


<script>
  var title_id = <?= $id ?>;
  $(function() {
    $( ".selected" ).draggable({
      containment: "#canvas",
      stop: function()
      {
        var p = $(this).position();
        var name = $(this).attr("id");
        var url = "im_gui.php?update=pos&id="+title_id+"&name="+name+"&x="+p.left+"&y="+p.top;
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


    $("#info-target").load("im_gui.php?load=attrs&id="+title_id+"&name="+name)
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
        var url = "im_gui.php?update=pos&id="+title_id+"&name="+name+"&x="+p.left+"&y="+p.top;
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
        var url = "im_gui.php?update=size&id="+title_id+"&name="+name+"&w="+$(this).width()+"&h="+$(this).height()+"&x="+p.left+"&y="+p.top;
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
      distance: 40
    });
    $(".layer").click(function(){
      var name = $(this).children("h3").html();
      $("#"+name).trigger('click');
    })
  });
</script>

<p><div class="slide" id="slider"></div>Zoom Level: <span id="zlevel">50</span><div class="slide" id="bg-slider"></div>Background Opacity: <span id="bglevel">50</span></p>

<div id="ccontainer">
	<div id="canvas" style="zoom:.5">
<?
$geos = listOfGeos($id);
foreach ($geos as $name => $type) {
	$sR = dbFetchAll($id, $name);
	echo("<div class=\"" . $type . " geo\" id=\"" . $name . "\" style=\" position:absolute; left: " . $sR["x"] . "; top: " . $sR["y"] . "; width: " . $sR["w"] . ";height: " . $sR["h"] . "\" >");
	echo("<img src=\"im_layout.php?id=" . $id . "&name=" . $name . "&type=" . $type . "\" />");
	echo("</div>");
}
?>
	</div>
</div>
<div id="info-panel">
  <div id="info-target" ></div>
</div>
<div id="layer-panel" >
<?
foreach ($geos as $name => $type) {
	echo "<div class=\"layer\" id=\"l-$name\"><h3>$name</h3><p>($type)</p></div>";
}
?>
</div>
