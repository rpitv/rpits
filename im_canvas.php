<?php
include("include.php");

$id = $_GET["id"];
$eventId = $_GET["eventId"];
?>
<!--<link rel="stylesheet" href="http://static.jquery.com/ui/css/demo-docs-theme/ui.theme.css" type="text/css" media="all">-->
<link rel="stylesheet" href="http://code.jquery.com/ui/1.8.21/themes/base/jquery-ui.css" type="text/css" media="all">
<link rel="stylesheet" href="css/canvas.css" type="text/css">
<script type="text/javascript" src="js/lib/jquery-1.8.3.js"></script>
<script type="text/javascript" src="js/lib/jquery.color-2.0b1.min.js"></script>
<script type="text/javascript" src="js/lib/jquery-ui-1.9.2.custom.min.js"></script>
<script>
  var title_id = <?= $id ?>;
	var eventId = <?= $eventId ?>;
</script>
<script type="text/javascript" src="js/canvas.js"></script>

<p><div class="slide" id="slider"></div>Zoom Level: <span id="zlevel">40</span><div class="slide" id="bg-slider"></div>Background Opacity: <span id="bglevel">50</span></p>

<div id="ccontainer">
	<div id="canvas" style="zoom:.4">
<?
$title = getTitle($id,$eventId);

foreach ($title['geos'] as $name => $geo) {
	echo("<div class=\"" . $geo['type'] . " geo\" id=\"" . $name . "\" style=\" position:absolute; left: " . $geo["x"] . "; top: " . $geo["y"] . "; width: " . $geo["w"] . ";height: " . $geo["h"] . ";z-index:".$geo['order']."\" >");
	echo("<img src=\"im_layout.php?id=$id&eventId=$eventId&name=$name&type=$geo[type]\" />");
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
foreach ($title['geos'] as $name => $geo) {
	echo "<div class=\"layer\" id=\"l-$name\"><h3>$name</h3><p>($geo[type])</p></div>";
}
?>
</div>
