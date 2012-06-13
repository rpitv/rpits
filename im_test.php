<pre>

<?php

include("include.php");
include("imagick_include.php");

$result = stripDBFetch(dbFetchAll(5,"AwayTeamName"));
  print_r($result);
?>
