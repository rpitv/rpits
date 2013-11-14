
<?php

include('init.php');

?>


<html>
<head>
<title>RPI TV Hockey Title Management System</title>
</head>
<body>
<h1>RPI TV Hockey Title Management System</h1>
<p style="font-family:'Arial',sans-serif;font-size:12px;font-weight:bold"><img src="rpits.jpg" style="vertical-align:-1px;"/> is proud to be a part of <img src="rpigs.jpg" style="vertical-align:-1px" /></p>
<h2>List of Current Pages</h2>
<ul>
	<li><a href="im_ui.php">Titles UI (ImageMagick based)</a> - Send titles to keyer, edit general titles. Also edit events and their participants</li>
	<li><a href="im_event_title.php">Event / Title List editor</a> - Customize the list of titles to be displayed in the UI</li>
	<li><a href="genall.php">Player Title Generator</a> - Generate the player titles for an entire team (or two), clear the cache</li>
	<li>Player/Roster Editor (<a href="im_peditor.php">New</a> / <a href="peditor.php">Old</a>) - Edit individual players' details and stats, control which players are on which teams</li>
	<li><a href="teamedit.php">'Old' Teams List Editor</a> - Edit the details (colors, logos) associated with each team <strong>for player titles ONLY</strong>, update stats from CHN</li>
	<li><a href="im_teams.php">'New' Teams editor</a> and <a href="im_organizations.php">Organizations editor</a> - Edit teams for general titles and events. Will shortly replace "'Old' Teams List Editor"
	<li><a href="addteamcsv.php">Add a Roster</a> - Adds a players and their stats to an empty team via CSV</li>
	<li><a href="im_stype.php">Stat Type label editor</a> - Edit labels associated with stats</li>
	<li><a href="im_billboards.php">Billboards Editor</a> - Full-frame 1920x1080 PNGs sent as is to the keyer - <strong>CURRENTLY BROKEN</strong></li>
</ul>

<img src="imagick.php" style="width:700px;height:60px;" alt="If this text is visible, your Imagick install is not configured correctly." />

</body>
</html>