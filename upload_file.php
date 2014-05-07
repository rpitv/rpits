<?php
$final_dir = dirname(__FILE__) . "/bugs/";

//only allow png files to be uploaded
$temp = explode(".", $_FILES["file"]["name"]);
$extension = end($temp);
if ($extension != 'png')
{
echo 'This only accepts png files <br>';
echo '<a href="javascript:history.go(-1)">Go back...</a>';
return;
}

//if we have errors, give the error code
//else display various details
  if ($_FILES["file"]["error"] > 0)
    {
    echo "Return Code: " . $_FILES["file"]["error"] . "<br>";
    }
  else
    {
    echo "Upload: " . $_FILES["file"]["name"] . "<br>";
    echo "Type: " . $_FILES["file"]["type"] . "<br>";
    echo "Size: " . ($_FILES["file"]["size"] / 1024) . " kB<br>";
    echo "Temp file: " . $_FILES["file"]["tmp_name"] . "<br>";

	//if file with same name exists, notify user
    if (file_exists($final_dir . $_FILES["file"]["name"]))
      {
      echo $_FILES["file"]["name"] . " already exists. If the file isn't the same, try a different name ";
	  echo "UPLOAD FAILED";
		return;
	  }
    else
      {
	  //move file to final location
	  echo "MOVE FILE TO: " . $final_dir . $_FILES["file"]["name"];
      move_uploaded_file($_FILES["file"]["tmp_name"],
      $final_dir . $_FILES["file"]["name"]);
      }
    }
?>

<html>
<hr>
<a href="javascript:history.go(-1)">Go back, the file should now be there...</a>
</html>