<?

function do_post_request($url, $data, $optional_headers = null)
{
  $params = array('http' => array(
              'method' => 'POST',
              'content' => $data
            ));
  if ($optional_headers !== null) {
    $params['http']['header'] = $optional_headers;
  }
  $ctx = stream_context_create($params);
  $fp = @fopen($url, 'rb', false, $ctx);
  if (!$fp) {
    throw new Exception("Problem with $url, $php_errormsg");
  }
  $response = @stream_get_contents($fp);
  if ($response === false) {
    throw new Exception("Problem reading data from $url, $php_errormsg");
  }
  return $response;
}


$id = $_GET["id"];
$type = $_GET["type"];
$command = $_GET["command"];

if(strlen($command) > 0)
{
  echo("Sending command $command.");
  $headers = "Content-Length: 0\n";
  $svg = "";

}
else
{
  $command = "key";
  if($type == "player") 
  {
    $svg = file_get_contents("http://localhost/hockey/svg_gen.php?id=$id&type=$type");
    echo("Attempting to put Player Title ID $id");
  }
  else
  {
    $svg = file_get_contents("http://localhost/hockey/svg_gen.php?id=$id&type=$type");
    echo("Attempting to put General Title ID $id");
  }
  
  $headers = "Content-Type: image/svg+xml\n";
  
}


$result = do_post_request("http://128.113.45.92:4567/$command",$svg,$headers);




?>