<?php
function update($player) {
  $names = explode(" ",$player[0][0],2);
  $first = $names[0];
  $last = $names[1];
  if($player[0][1] == "G" && $player[6] == 0) {
    return "<tr><td>$first $last is a goalie</td></tr>";
	}
  $result = dbquery("SELECT * FROM players WHERE first='$first' AND last='$last'");
  $row = mysql_fetch_array($result);
  if($row) {
    if($player[0][1] == "G") {
      $query = "UPDATE players SET stype='hg', s1='$player[1]', s2='$player[2]', s3='$player[3]', s4='$player[4]', s5=$player[10], s6='$player[7]' WHERE first='$first' AND last='$last'";
		}	else {
			if($player[10] > 0) {
				$player[10] = '+' . $player[10];
			}
      $query = "UPDATE players SET stype='hp', s1='$player[1]', s2='$player[2]', s3='$player[3]', s4='$player[4]', s5='$player[6]', s6='$player[10]' WHERE first='$first' AND last='$last'";
		}
    $result = dbquery($query);
    $gamesdiff = $player[1] - $row["s1"];
    $ptsdiff = $player[4]-$row["s4"];
    return ("<tr><td>$first $last updated +" . $gamesdiff . " games, +" . $ptsdiff ." pts.</td></tr>");
  } else {
    return "<tr><td>$first $last not found</td></tr>";
	}
}

function parser($data) {
	preg_match_all("/{c:\[{v:.*}\]}/",$data,$data2);
  foreach($data2[0] as $line) {
    $line = substr($line,4);
    $num = preg_match_all("/{v:\s([^}{]+)}, /",$line,$vals);
    $vals[1][0] = substr($vals[1][0],1);
    $player = explode(", ",$vals[1][0]);
    $player[2] = substr($player[2],0,-1);
    $vals[1][0] = $player;
    $result[] = $vals[1];
  }
  return $result;
}
?>
