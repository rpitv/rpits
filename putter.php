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
$command = $_GET["command"];





if(strlen($command) > 0)
{
  $headers = "Content-Length: 0\n";
  $svg = "";
}
else
{
  $command = "key";
  $svg = file_get_contents("http://localhost/hockey/svg_card.php?id=$id");
  $headers = "Content-Type: image/svg+xml\n";
}


echo(do_post_request("http://67.246.53.205:4567/$command",$svg,$headers));

echo("<p>Attempting to put the following svg</p>");
echo($svg);




/*$params = array(
    'http' => array(
        'method' => 'PUT',
        'header' => "Authorization: Basic " . base64_encode($this->ci->config->item('ws_login') . ':' . $this->ci->config->item('ws_passwd')) . "\r\nContent-type: text/xml\r\n",
        'content' => file_get_contents($tmpFile)
    )
);
$ctx = stream_context_create($params);
$response = @file_get_contents($url, false, $ctx);

return ($response == '');*/

?>