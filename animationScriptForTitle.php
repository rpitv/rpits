<?php

include('init.php');
include('include.php');


$title = getStatscard($_GET['titleId']);

echo getAnimationScriptForTitle($title,false);

?>
