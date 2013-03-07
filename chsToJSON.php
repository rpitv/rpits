<?php

$url = $_GET['url'];

$page = fopen($url, "r");
$contents = stream_get_contents($page);
preg_match('/tiny">(.*?)<\/PRE>/s', $contents, $match);

$l = explode("\n", $match[1]);

/* // debug section
	for ($i = 0; $i <= 128; $i++) {
	echo floor($i / 100);
	}
	echo "\n";
	for ($i = 0; $i <= 128; $i++) {
	echo floor($i / 10 % 10);
	}
	echo "\n";
	for ($i = 0; $i <= 128; $i++) {
	echo ($i % 10);
	}
	echo "\n";

	for ($i = 56; $i < count($l); $i++) {
	echo $l[$i] . "\n";
	}
 */
$team = array();
$team['name'] = trim(substr($l[1], 0, 30));
$team['w'] = intval(substr($l[1], 57, 2));
$team['l'] = intval(substr($l[1], 60, 2));
$team['t'] = intval(substr($l[1], 63, 2));

$team['cw'] = intval(substr($l[1], 96, 2));
$team['cl'] = intval(substr($l[1], 99, 2));
$team['ct'] = intval(substr($l[1], 102, 2));

$players = array();


//players
for ($i = 4; $l[$i][0] != '-'; $i++) {
	$s = array();
	$s['num'] = intval(substr($l[$i], 0, 2));
	$s['name'] = trim(substr($l[$i], 3, 26));
	$s['pos'] = trim(substr($l[$i], 30, 2));
	$s['year'] = trim(substr($l[$i], 33, 2));
	$s['gp'] = intval(substr($l[$i], 38, 2));
	$s['g'] = intval(substr($l[$i], 41, 3));
	$s['a'] = intval(substr($l[$i], 45, 3));
	$s['pts'] = intval(substr($l[$i], 49, 3));
	$s['pen'] = intval(substr($l[$i], 53, 3));
	$s['pim'] = intval(substr($l[$i], 57, 3));
	$s['pp'] = intval(substr($l[$i], 62, 2));
	$s['sh'] = intval(substr($l[$i], 65, 2));
	$s['gw'] = intval(substr($l[$i], 68, 2));
	$s['gt'] = intval(substr($l[$i], 71, 2));
	if ($s['num'] != 0) {
		$players[$s['num']] = $s;
	}
}

// team totals
for ($i++; $l[$i][0] != '-'; $i++) {

}

// goalies (overall)
for ($i++; $l[$i][0] != '-'; $i++) {
	$g = array();
	$g['num'] = intval(substr($l[$i], 0, 2));
	$g['name'] = trim(substr($l[$i], 3, 26));
	$g['year'] = trim(substr($l[$i], 33, 2));
	$g['gp'] = intval(substr($l[$i], 39, 2));
	$g['minutes'] = trim(substr($l[$i], 43, 7));
	$g['ga'] = intval(substr($l[$i], 51, 3));
	$g['saves'] = intval(substr($l[$i], 56, 4));
	$g['shots'] = intval(substr($l[$i], 62, 4));
	$g['spct'] = trim(substr($l[$i], 69, 4));
	$g['gaa'] = trim(substr($l[$i], 74, 5));
	$g['w'] = intval(substr($l[$i], 80, 2));
	$g['l'] = intval(substr($l[$i], 83, 2));
	$g['t'] = intval(substr($l[$i], 86, 2));
	$g['gs'] = intval(substr($l[$i], 95, 3));
	$g['so'] = intval(substr($l[$i], 100, 2));
	$g['pcttime'] = trim(substr($l[$i], 103, 5));
	if ($g['num'] != 0) {
		$players[$g['num']] = array_merge($players[$g['num']], $g);
	}
}

// goalies (conf)
for ($i++; $l[$i][0] != '-'; $i++) {

}

// goalies (career)
for ($i++; $l[$i][0] != '-'; $i++) {

}

$opp = array();

// Special Teams
for ($i+=2; $l[$i][0] != '-'; $i++) {
	$t = array();
	$t['name'] = trim(substr($l[$i], 0, 34));
	$t['pp'] = trim(substr($l[$i], 38, 7));
	$t['pppct'] = trim(substr($l[$i], 46, 4));
	$t['pk'] = trim(substr($l[$i], 51, 7));
	$t['pkpct'] = trim(substr($l[$i], 59, 4));
	$t['st'] = trim(substr($l[$i], 64, 7));
	$t['stpct'] = trim(substr($l[$i], 72, 4));
	$t['ppcg'] = trim(substr($l[$i], 78, 4));
	if ($team['name'] == $t['name']) {
		$team = array_merge($team, $t);
	} else {
		$opp = $t;
	}
}

//print_r($team);
//print_r($opp);
//print_r($players);

$json = array('team' => $team, 'opp' => $opp, 'players' => $players);

echo json_encode($json);
?>
