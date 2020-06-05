<?php
header('Content-Type: text/html; charset=utf-8');
?>
<title>Upload Team Headshots</title>

<script src="./js/lib/jquery-1.8.3.js"></script>
<script src="./parseRoster.js"></script>
<script src="./state_province.js"></script>

<h1>Upload Team Headshots</h1>

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
	<label>Team Name: <input id="team_box" type="text" name="team_sel" size="10" /> </label>
	<p></p>
	<label for="file">Filename:</label>
	<input type="file" name="file[]" id="file" multiple><br>
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
	if($_POST['team_sel'] == '')
	{
		echo "No Team. Enter one and try again. <br>";
		return;
	}
	if(file_exists('teams/'.$_POST['team_sel'].'imgs'))
	{
		echo "work <br>";
		// Count total files
		$countfiles = count($_FILES['file']['name']);

		 // Looping all files
		for($i=0;$i<$countfiles;$i++)
		{			
			$filename = $_FILES['file']['name'][$i];		 
			//Upload file
			if(file_exists($_FILES['file']['tmp_name'][$i],'teams/'.$_POST['team_sel'].'imgs/'.$filename))
			{
				unlink($_FILES['file']['tmp_name'][$i],'teams/'.$_POST['team_sel'].'imgs/'.$filename);
			}
			move_uploaded_file($_FILES['file']['tmp_name'][$i],'teams/'.$_POST['team_sel'].'imgs/'.$filename);	
			echo 'teams/'.$_POST['team_sel'].'imgs/'.$filename ;
		}
	
	}
	else
	{
		mkdir('teams/'.$_POST['team_sel'].'imgs');
		// Count total files
		$countfiles = count($_FILES['file']['name']);
		 // Looping all files
		for($i=0;$i<$countfiles;$i++)
		{			
			$filename = $_FILES['file']['name'][$i];		 
			// Upload file
			if(file_exists($_FILES['file']['tmp_name'][$i],'teams/'.$_POST['team_sel'].'imgs/'.$filename))
			{
				//unlink($_FILES['file']['tmp_name'][$i],'teams/'.$_POST['team_sel'].'imgs/'.$filename);
			}
			move_uploaded_file($_FILES['file']['tmp_name'][$i],'teams/'.$_POST['team_sel'].'imgs/'.$filename);			
		}
	}

}
/*

//if we have errors, give the error code
//else display various details
if ($_FILES["file"]["error"] > 0) {
	echo "Return Code: " . $_FILES["file"]["error"][0] . "<br>";
} 
else 
{
	echo "Upload: " . $_FILES["file"]["name"][0] . "<br>";
	echo "Type: " . $_FILES["file"]["type"][0] . "<br>";
	echo "Size: " . ($_FILES["file"]["size"][0] / 1024) . " kB<br>";
	echo "Temp file: " . $_FILES["file"]["tmp_name"][0] . "<br>";

	//if file with same name exists, notify user
	if (file_exists($final_dir . $_FILES["file"]["name"][0])) {
		echo $_FILES["file"]["name"][0] . " already exists. If the file isn't the same, try a different name ";
		echo "UPLOAD FAILED";
		return;
	} 
	else 
	{ //move file to final location
		echo "MOVE FILE TO: " . $final_dir . $_FILES["file"]["name"][0];
		#move_uploaded_file($_FILES["file"]["tmp_name"][0], $final_dir . $_FILES["file"]["name"][0]);
	}
}
*/
?>

