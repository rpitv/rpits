
<?php

include('init.php');

// phpinfo();

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
	<li><a href="im_ui.php">Titles UI (ImageMagick based)</a> - Main user interface for editing titles and sending them to the keyer.</li>
	<li><a href="im_event_title.php">Event / Title List editor</a> - Customize the list of titles to be displayed in the UI.</li>
	<li><a href="im_peditor.php">Player/Roster Editor</a> - Edit individual players' details/stats, control which players are on which teams</li>
	<li><a href="im_teams.php">Teams editor</a> and <a href="im_organizations.php">Organizations editor</a> - Edit teams and their respective organizations.</li>
	<li><a href="addTeamCsv.php">Add a Roster</a> - Add players and their stats to a team.</li>
	<li><a href="stypeEditor.php">Stat Type label editor</a> - Edit labels associated with stats.</li>
	<li><a href="genall.php">Player Title Generator</a> - Generate the player titles for an entire team (or two), clear the cache.</li>
	<li><a href="bugSelector.php">Bug Selector</a> - Select a 1920x1080 .PNG file for use as a broadcast bug.</li>
	<li><a href="upload_headshots.php">Upload Headshots</a> - Upload headshots for a team.</li>
	<li><a href="upload_logos.php">Upload Logos</a> - Upload Logo for a team.</li>
</ul>
<h3>Deprecated Pages</h3>
<ul>
	<li><strong>CURRENTLY BROKEN</strong> - <a href="im_billboards.php">Billboards Editor</a> - Full-frame 1920x1080 PNGs sent as is to the keyer</li>
	<li><a href="teamedit.php">Old Teams List Editor</a> - Edit the details (colors, logos) associated with each team <strong>for player titles ONLY</strong></li>
	<li><a href="peditor.php">Old Player/Roster Editor</a> - Old-style player/roster editor, this shouldn't have to be used, if so, there's a problem.</li>
</ul>

<img src="imagick.php" style="width:700px;height:60px;" alt="If this text is visible, your Imagick install is not configured correctly." />

</body>
</html>
