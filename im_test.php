<pre>

<?php

// This file is just for checking that functions / ideas work independently


//include("include.php");
//include("imagick_include.php");



//$result = stripDBFetch(dbFetchAll(5,"AwayTeamName"));
//  print_r($result);

$page = fopen("tabular_data.txt","r");
$contents = stream_get_contents($page);

$result = parser($contents);

foreach($result as $img)
{
  echo "<img src=\"http://rpiathletics.com/" . $img . "\" />$img<br>";
}

function parser($data)
{
  preg_match_all("/image_path=(.*jpg)/i",$data,$data2);
  foreach($data2[1] as $line)
  {
    $num = preg_match_all("/\/\/([A-Za-z]*)_/",$line,$vals);
    print_r($vals);
  }
  return $data2[1];
}

?>

/common/controls/image_handler.aspx?image_path=/images/2012/8/30//KyleMcGovern_23.jpg&thumb_prefix=rp_roster


