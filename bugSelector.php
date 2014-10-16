<?
//Set no caching
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); 
header("Cache-Control: no-store, no-cache, must-revalidate"); 
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>
<html>
<head> <title>RPITS Bug Selector</title></head>
<h1>RPITS Bug Selector</h1>
<hr>
</html>


<? php
;

$setVal = "";
if ($_GET['setBug'] != "") {
$setVal = $_GET['setBug'];
}

$delVal = "";
if ($_GET['deleteBug'] != "") {
$delVal = $_GET['deleteBug'];
}

//delete file and auto go back
if ($delVal != ""){
unlink($delVal);
echo "<script type ='text/javascript'> javascript:history.go(-1) </script>";
}

//set the bug and auto go back
if ($setVal != ""){
do_post_request("http://ip6-localhost:3005/key", file_get_contents($setVal));
echo "<script type ='text/javascript'> javascript:history.go(-1) </script>";
}


//display all images in folder
$dirname = dirname(__FILENAME__) . '/bugs/';
$images = glob($dirname."*");
foreach($images as $image) {
echo '<img src="'.$image.'" width=500 border=1 style="background-color: #eee;
							background-image: linear-gradient(45deg, lightgray 25%, transparent 25%, transparent 75%, lightgray 75%, lightgray), 
							linear-gradient(45deg, lightgray 25%, transparent 25%, transparent 75%, lightgray 75%, lightgray);
							background-size:20px 20px;
							background-position:0 0, 30px 30px" /><br />';
echo substr($image, strrpos($image, "/") + 1, 99);
echo "<br>";

//use this bug link
echo '<a href=" ' .$_SERVER['REQUEST_URI']. "?setBug=" .$image. ' ">Use This Bug</a> <br>';

//hard coded to not allow deletion of bug.png
if (substr($image, strrpos($image, "/") + 1, 99) != "bug.png"){
//delete this bug link
echo '<a href=" ' .$_SERVER['REQUEST_URI']. "?deleteBug=" .$image. ' " onclick="return confirm(\'Are you sure you want to delete this file?\')">Delete This Bug</a> <br>';
}

//line
echo '<hr>';
}


//perform post request to the keyer
function do_post_request($url, $data, $optional_headers = null) {
	$params = array('http' => array(
					'method' => 'POST',
					'content' => $data
					));
	if ($optional_headers !== null) {
		$params['http']['header'] = $optional_headers;
	}
	$ctx = stream_context_create($params);
	$fp = @fopen($url, 'rb', false, $ctx);
	if ($http_response_header[0] == "HTTP/1.1 503 Service Unavailable") {
		echo "Error 503";
	}
	if (!$fp) {
		//throw new Exception("Problem with $url, $php_errormsg <br>");
	}
	$response = @stream_get_contents($fp);
	if ($response === false) {
		//throw new Exception("Problem reading data from $url, $php_errormsg <br>");
	}
	echo "done";
	return $response;
}


?>

<html>
<body>
<p>Upload another png to be used as a bug...</p>

<script>
window.onload = function() {
//this is all upload file stuff
	var url = document.URL.substring(0, document.URL.lastIndexOf("/")) + "/upload_file.php";
       //when the document is finished loading, replace everything
       //between the <a ...> </a> tags with the setValue of splitText
   document.getElementById("myurl").action=url;
}

</script>
<form id="myurl" action="" method="post" 

enctype="multipart/form-data">
<input type="hidden" name="url" id="url" setValue="" />
<label for="file">Filename:</label>
<input type="file" name="file" id="file"><br>
<input type="submit" name="submit" setValue="Submit">
<hr>
<p></p>
<p></p>

</form>

<script>
document.getElementById("url").setValue = document.URL;
console.log(document.URL);
</script>

</body>
</html>
