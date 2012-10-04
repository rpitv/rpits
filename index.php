
<?php

include('init.php');

?>


<html>
<head>
<title>RPI TV Hockey Title Management System</title>
</head>
<body>
<h1>RPI TV Hockey Title Management System</h1>
<h2>List of Current Pages</h2>
<ul>
	<li><a href="im_ui.php">Titles UI (ImageMagick based)</a> - Send titles to keyer, edit general titles. Optimized for 1680x1050 full screen</li>
	<li><a href="genall.php">Player Title Generator</a> - Generate the player titles for an entire team (or two), clear the cache</li>
	<li><a href="peditor.php">Player/Roster Editor</a> - Edit individual players' details and stats, control which players are on which teams</li>
	<li><a href="teamedit.php">Teams List Editor</a> - Edit the details (colors, logos) associated with each team, update stats from CHN</li>
	<li><a href="addteamcsv.php">Add a Roster</a> - Adds a players and their stats to an empty team via CSV</li>
</ul>
<p>Outdated/Discontinued Pages</p>	
<ul>
	<li><a href="ui.php">Titles UI (GD/SVG based)</a> - Encapsulate SD titles in an SVG and send to keyer, no longer supported
	<li><a href="generaltitle.php">General Title Generator</a> - Edit non-player titles such as lineups, opening titles, statistics, PP/PK, etc</li>
</ul>
<img src="imagick.php" style="width:700px;height:60px;" alt="If this text is visible, your Imagick install is not configured correctly." />

</body>
</html>