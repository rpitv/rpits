<?
mysql_connect("localhost","root","");
mysql_select_db("rpits");

function dbquery($query)
{
  $result = mysql_query($query) or die("<b>Error with MySQL Query:</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
  return $result;
}
function dbqueryl($query)
{
  $result = mysql_query($query);
  return $result;
}
function getContent($id,$field)
{
  $query = "SELECT * FROM cdb WHERE title_id='$id' AND field='$field' LIMIT 1";
  $result = dbquery($query);
  $assoc = mysql_fetch_assoc($result);
  return $assoc["data"];
}
function getColor($id,$field,$im)
{
  $data = getContent($id,$field);
  $rgb = explode(",",$data);
  $color = imagecolorallocate($im, $rgb[0], $rgb[1], $rgb[2]);
  return $color;
}
function getFont($font)
{
  $query = "SELECT * FROM fonts WHERE name='$font' LIMIT 1";
  $result = dbquery($query);
  $assoc = mysql_fetch_assoc($result);
  return $assoc["path"];
}
function getLinesFromText($path)
{
  $handle = fopen($path,"r");
  $commands = array();
  while(!feof($handle))
  {
    $line = preg_split("/[\s\n]/",fgets($handle));
    $assoc = array();
    $assoc["command"] = $line[0];
    if($line[0] == "poly")
    {
      $assoc["color"] = $line[2];
      $assoc["points"] = preg_split("/[,;]/",$line[1]);
    }
    elseif($line[0] == "asset" || $line[0] == "img")
    {
      $assoc["asset"] = $line[1];
      $assoc["points"] = preg_split("/[,;]/",$line[2]);
    }
    elseif($line[0] == "text")
    {
      $assoc["content"] = $line[1];
      $assoc["font"] = $line[6];
      $assoc["points"] = preg_split("/[,;]/",$line[5]);
      $assoc["align"] = $line[2];
      $assoc["shadow"] = $line[4];
      $assoc["size"] = $line[3];
    }
    $commands[] = $assoc;
    //print_r($assoc);
    //echo("<br>");
  }
  return $commands;
}
?>