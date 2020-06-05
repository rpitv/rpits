<?php
header('Content-Type: text/html; charset=utf-8');
?>
<title>Upload Team Logo</title>

<script src="./js/lib/jquery-1.8.3.js"></script>
<script src="./parseRoster.js"></script>
<script src="./state_province.js"></script>

<h1>Upload Team Logo</h1>

<br>
<script>
	window.onload = function() {
		//this is all upload file stuff
		//var url = document.URL.substring(0, document.URL.lastIndexOf("/")) + "/upload_file.php";
		//when the document is finished loading, replace everything
		//between the <a ...> </a> tags with the setValue of splitText
	   //document.getElementById("myurl").action=url;
	}
</script>
<form id="myurl" action="" method="post" enctype="multipart/form-data">
	<label for="file">Filename:</label>
	<input type="file" name="file" id="file"><br>
	<input type="submit" name="submit" value="Upload">
	<hr>
	<p></p>
	<p></p>
</form>



<?php
include("init.php");

function console_log($output, $with_script_tags = true) {
    $js_code = 'console.log(' . json_encode($output, JSON_HEX_TAG) . ');';
    if ($with_script_tags) {
        $js_code = '<script>' . $js_code . '</script>';
    }
    echo $js_code;
}

if(isset($_POST['submit'])){
	
	$filename = $_FILES['file']['name'];		 
	// Upload file	
	move_uploaded_file($_FILES['file']['tmp_name'],'teamlogos/'.$_POST['team_sel'].$filename);			

}
?>

