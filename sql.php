<?php

include('init.php');
include('include.php');

$firstWord = explode(' ',$_GET["sql"]);

if($firstWord[0] == 'SELECT') {
	echo queryToJsonArray($_GET["sql"]);
} else if ($firstWord[0] == 'INSERT') {
	dbquery($_GET["sql"]);
	$id['id'] = rpits_db_insert_id();
	echo json_encode($id);
} else {
	dbquery($_GET["sql"]);
}

?>
