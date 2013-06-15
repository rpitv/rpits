<?php

$svg_src = $_GET["svg"];

$data["HomeTeamName"] = "RENSSELAER";
$data["VisitingTeamName"] = "DARTMOUTH";
$data["Venue"] = "Houston Field HOUSE!";
$data["Date"] = "1/27/2012";
$data["City"] = "Troy, NY";
$data["HomeTeamColor"] = "rgb(0,0,0)";
$data["VisitingTeamColor"] = "rgb(220,220,220)";

$xml = simplexml_load_file($svg_src);

$paths = $xml->xpath("/svg/path[@name]");

foreach($paths as &$path)
{
  $name = "" . $path['name'];
  $path['fill'] = $data[$name];
}

$texts = $xml->xpath("/svg/text[@name]");

foreach($texts as &$text)
{
    $name = "".$text['name'];
    $text[0] = $data[$name];
}

print $xml->asXML();;

?>
