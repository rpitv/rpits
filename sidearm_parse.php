<?php

$page = fopen("http://www.sidearmstats.com/rpi/mhockey/1.xml","r");
$contents = stream_get_contents($page);

$sidearm = new SimpleXMLElement($contents);
echo "<pre>";
echo("Home team: " . $sidearm->venue["visname"] . " (" . $sidearm->venue["visid"] . ")\n");
echo("Visiting team: " . $sidearm->venue["homename"] . " (" . $sidearm->venue["homeid"] . ")\n");

foreach($sidearm->venue->officials->official as $official)
  echo $official["name"] . " - " . $official["title"] . "\n";

echo("\nTeam 1 lines: \n");
foreach($sidearm->team->lines->line as $line)
  echo($line["lw"] . " " . $line["c"] . " " . $line["rw"] . "\n");
foreach($sidearm->team->lines->line as $line)
  echo($line["ld"] . " " . $line["rd"] . "\n");
foreach($sidearm->team->lines->line as $line)
  echo($line["g"] . "\n");

echo("\nTeam 2 lines: \n");
foreach($sidearm->team[1]->lines->line as $line)
  echo($line["lw"] . " " . $line["c"] . " " . $line["rw"] . "\n");
foreach($sidearm->team[1]->lines->line as $line)
  echo($line["ld"] . " " . $line["rd"] . "\n");
foreach($sidearm->team[1]->lines->line as $line)
  echo($line["g"] . "\n");

echo("\nLinescore:\n");
echo($sidearm->venue["visname"] . "  | ");
echo($sidearm->team[0]->linescore->lineprd[0]["score"] . " | ");
echo($sidearm->team[0]->linescore->lineprd[1]["score"] . " | ");
echo($sidearm->team[0]->linescore->lineprd[2]["score"] . " | ");
echo($sidearm->team[0]->linescore["score"] . " |\n");
echo($sidearm->venue["homename"] . " | ");
echo($sidearm->team[1]->linescore->lineprd[0]["score"] . " | ");
echo($sidearm->team[1]->linescore->lineprd[1]["score"] . " | ");
echo($sidearm->team[1]->linescore->lineprd[2]["score"] . " | ");
echo($sidearm->team[1]->linescore["score"] . " |\n\n");

echo("Penalty Stats\n");
echo($sidearm->venue["visname"] . ": " . $sidearm->team[0]->totals->powerplay["ppg"] . "/");
echo($sidearm->team[0]->totals->powerplay["ppopp"]."\n");
echo($sidearm->venue["homename"] . ": " . $sidearm->team[1]->totals->powerplay["ppg"] . "/");
echo($sidearm->team[1]->totals->powerplay["ppopp"]."\n");


foreach($sidearm->team[0]->player as $player)
{
  if($player["gp"] == 1 && $player["name"] != "TEAM")
  {
    $name = explode(', ',$player["name"]);
    echo($player["uni"] . " - " . $name[1] . " " . $name[0] . " - " . $player["pos"] ."\n");
  }
  
}



echo("\n\n");
print_r($sidearm);
echo "</pre>";

?>
