<?php
//Set no caching
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); 
header("Cache-Control: no-store, no-cache, must-revalidate"); 
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
include("include.php");
?>
<html>
<head>
<title>RPITS Bug Selector</title>
<script src="js/lib/jquery-1.8.3.js" type="text/javascript"></script>
<style>
img {
	width: 500px;
	border: 1px solid black;
	margin: 0 auto;
	background-color: #eee;
	background-image: linear-gradient(45deg, lightgray 25%, transparent 25%, transparent 75%, lightgray 75%, lightgray), 
		linear-gradient(45deg, lightgray 25%, transparent 25%, transparent 75%, lightgray 75%, lightgray);
	background-size:20px 20px;
	background-position:0 0, 30px 30px;
	border: 8px solid rgba(0, 0, 0, .5);
}
.bugBox {
	display: inline-block;
	width: 520px;
	margin-top: 10px;
	text-align: center;
}
.bugBox .words {
	width: 502px;
	margin: 0 auto;
	text-align: left;
}
#allBugs {
	clear: both;
}
</style>
</head>

<body>
<h1>RPITS Bug Selector</h1>
<hr>

<?php

$setVal = "";
if ($_GET['setBug'] != "") {
	$setVal = $_GET['setBug'];
}

$delVal = "";
if ($_GET['deleteBug'] != "") {
	$delVal = $_GET['deleteBug'];
}

//delete file and auto go back
if ($delVal != "") {
	unlink($delVal);
	echo "<script type ='text/javascript'> javascript:history.go(-1) </script>";
}

//set the bug and auto go back
if ($setVal != "") {
	do_post_request("http://ip6-localhost:3005/key", file_get_contents($setVal));
	echo "<script type ='text/javascript'> javascript:history.go(-1) </script>";
}


?>
<div id="currentBug" class="bugBox"></div>
<script type="text/javascript">
	$(document).ready( function() {
		$("#currentBug").html('<img id="current" src="loadCurrentBug.php?n="' + Math.random() + '"/><br>Current Bug');
	});
</script>
<?php
$bug_state_json = @file_get_contents($bug_keyer_url . 'state');
if ($bug_state_json !== FALSE) {
	$bug_info = json_decode($bug_state_json);
	if ($bug_info->state === 'up') { // show if bug is LIVE
?>
		<script type="text/javascript">
			$(document).ready( function() {
				$('#current').css('border', '8px solid red');
			});
		</script>
<?php
	}
}
?>
</div>
<hr>
<div id="allBugs">
<?php

//display all images in folder
$dirname = dirname(__FILENAME__) . '/bugs/';
$images = glob($dirname."*");
foreach($images as $image) {
	echo '<div class="bugBox"><img src="'.$image.'" /><br>';
	echo substr($image, strrpos($image, "/") + 1, 99);
	echo "<br>";

	//use this bug link
	echo '<div class="words"><a href=" ' .$_SERVER['REQUEST_URI']. "?setBug=" .$image. ' ">Use This Bug</a> <br>';

	//hard coded to not allow deletion of bug.png
	if (substr($image, strrpos($image, "/") + 1, 99) == "bug.png") {
		echo '<br>'; //don't include delete bug link
	} else {
		echo '<a href=" ' .$_SERVER['REQUEST_URI']. "?deleteBug=" .$image. ' " onclick="return confirm(\'Are you sure you want to delete this file?\')">Delete This Bug</a> <br>';
	}

	//line
	echo '</div></div>';
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

</div>
<hr>
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
<form id="myurl" action="" method="post" enctype="multipart/form-data">
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
