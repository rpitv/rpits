<?php

function rpits_mysql_connect($server, $user, $password, $database) {
    global $rpits_db_connection;

    if (isset($rpits_db_connection)) {
        trigger_error("rpits_mysql_connect was called twice.", E_WARNING);
    }

    $rpits_db_connection = new mysqli($server, $user, $password, $database);

    if ($mysqli->connect_error) {
        die('database connection error: ' . $mysqli->connect_error);
    }

    $rpits_db_connection->query('SET NAMES "utf8" COLLATE "utf8_general_ci";');
}

function _rpits_db_check() {
    global $rpits_db_connection;
    if (!isset($rpits_db_connection)) {
        die('no database connection');
    }
}

function rpits_db_errno() {
    global $rpits_db_connection;
    _rpits_db_check();
    return $rpits_db_connection->errno;
}

function rpits_db_error() {
    global $rpits_db_connection;
    _rpits_db_check();
    return $rpits_db_connection->error;
}

function rpits_db_fetch_array($result) {
    global $rpits_db_connection;
    return $result->fetch_array();
}

function rpits_db_fetch_assoc($result) {
    global $rpits_db_connection;
    return $result->fetch_assoc();
}

function rpits_db_insert_id() {
    global $rpits_db_connection;
    _rpits_db_check();
    return $rpits_db_connection->insert_id;
}

function rpits_db_num_rows($result) {
    global $rpits_db_connection;
    return $result->num_rows();
}

function rpits_db_query($query_string) {
    global $rpits_db_connection;
    return $rpits_db_connection->query($query_string);
}

?>
