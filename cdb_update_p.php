<?php

include("include.php");
mysql_select_db("rpihockey");

if ($_POST)
{
  $id = $_POST["id"];
  foreach($_POST as $key => $value)
  {
    if ($key != "id")
    {
      dbquery("UPDATE players SET $key='$value' WHERE `id`=$id;");
    }
  }
}

echo ("Updated");

?>
