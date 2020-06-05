<?php

include ("init.php");
include ("include.php");

$team = $_GET["team"];
$url = $_GET["url"];

$contents = file_get_contents($system_path_prefix . "chsToJSON.php?url=$url");

$data = json_decode($contents,true);

//print_r($data);

$players = $data['players'];

foreach($players as $p) {
	$playerResource = dbQuery("SELECT * FROM players WHERE team='$team' AND num='".$p['num']."'");
	$row = $playerResource->fetch_assoc();
	if($row) {
		if ($p["pos"] != 'G') {
			dbQuery("UPDATE players SET stype='ho', s1='".$p['gp']."', s2='".$p['g']."', s3='".$p['a']."', s4='".$p['pts']."', s5='".$p['pim']."' WHERE team='$team' AND num='".$p['num']."'");
		} else {
			dbQuery("UPDATE players SET stype='hg', s1='".$p['gp']."', s2='".$p['w']."', s3='".$p['l']."', s4='".$p['t']."', s5='".$p['spct']."', s6='".$p['gaa']."' WHERE team='$team' AND num='".$p['num']."'");
		}
		echo $p['name'] . ', ' . $p['gp'] . ' games played. <br>';
	}	else {
		echo $p['name'] . ' not found.<br>';
	}
}

?>
