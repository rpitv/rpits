<?php

include("include.php");

$id = key($_POST);
$name = $_POST[$id];
next($_POST);
$key = key($_POST);
$value = $_POST[$key];

dbquery("REPLACE into cdb (`title_id`,`name`,`key`,`value`) VALUES (\"$id\",\"$name\",\"$key\",\"$value\");");

echo ("Updated");

?>
