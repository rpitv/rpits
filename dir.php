<?php


  $handle = opendir('.');
  while (false !== ($file = readdir($handle))) {
    if(is_dir($file))
      echo("<a href=\"gallery.php?dir=$file&w=400\"/>$file</a><br>\n\n");
  }
?>