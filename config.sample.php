<?php

$mysql_server_address = "127.0.0.1";
$mysql_server_username = "root";
$mysql_server_password = "";
$mysql_database_name = "rpits";
include_once("db_mysqli.php");
rpits_mysql_connect(
    $mysql_server_address, $mysql_server_username,
    $mysql_server_password, $mysql_database_name
);

$system_path_prefix = "http://127.0.0.1/rpits/";

// Only necessary in order to communicate with an Exavideo keyer, can be ignored
$keyer_url = "http://[::1]:4567/"; // trailing slashes are important
$bug_keyer_url = "http://[::1]:3005/";
$animator_url = "http://127.0.0.1:3004/"; // trailing slashes are important

define('IMGFMT','png'); // 'tga' also supported

?>
