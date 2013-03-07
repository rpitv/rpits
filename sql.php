<?php

include('init.php');
include('include.php');

mysql_select_db($_GET["db"]);

$firstWord = explode(' ',$_GET["sql"]);

if($firstWord[0] == 'SELECT') {
	echo mysqlQueryToJsonArray($_GET["sql"]);
} else if ($firstWord[0] == 'INSERT') {
	dbquery($_GET["sql"]);
	$id['id'] = mysql_insert_id();
	echo json_encode($id);
} else {
	dbquery($_GET["sql"]);
}

?>
