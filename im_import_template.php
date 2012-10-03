<pre>
<?php
include("include.php");
include("imagick_include.php");

$template_id = $_GET["id"];

$result = dbquery("SELECT * from templates where id=\"$template_id\" LIMIT 1;");
$templateRow = mysql_fetch_array($result);

$templateXML = fopen($templateRow["path"],"r");
$contents = stream_get_contents($templateXML);

$xml = new SimpleXMLElement($contents);

$name = $templateRow["name"];

/*
dbquery("INSET INTO titles (name) VALUES ('$name'");
$title_id = mysql_insert_id();
*/
foreach($xml->geo->children() as $name=>$geo)
{
  echo $name;
  
  print_r($geo);
}

?>
</pre>