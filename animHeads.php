<?php 
///var/www/machac3/rpits/statscard_no_img
if (isset($_GET['folderdata'])){
	$folderdata = scandir("/var/www/machac3/rpits/statscard_no_img/rpi-mh");
	echo json_encode($folderdata);
	exit(0);
}

function do_post_request($url, $data, &$log, $optional_headers = null) {
	$params = array('http' => array(
					'method' => 'PUT',
					'content' => $data
					));
	if ($optional_headers !== null) {
		$params['http']['header'] = $optional_headers;
	}
	$ctx = stream_context_create($params);
	$fp = @fopen($url, 'rb', false, $ctx);
	if ($http_response_header[0] == "HTTP/1.1 503 Service Unavailable") {
		$log .= "<b style=\"color:red\">503</b>, ";
	}
	else{
	$log = "Should be okay";
	}
	if (!$fp) {
		//throw new Exception("Problem with $url, $php_errormsg <br>");
	}
	$response = @stream_get_contents($fp);
	if ($response === false) {
		//throw new Exception("Problem reading data from $url, $php_errormsg <br>");
	}
	return $response;
}

$log = "";

if (isset($_POST['player'])){

$data = file_get_contents("/var/www/machac3/rpits/test_head.js");

$person = $_POST['player'];

$data = str_replace("PERSONGOESHERE", $person, $data);

$response = do_post_request("http://ip6-localhost:3004/script", $data, $log);
}

if (isset($_POST['fade_out'])){
	if($_POST['fade_out'] == 'cut'){
		$response = do_post_request("http://ip6-localhost:3004/command", "cut", $log);
	}
	else{
		$response = do_post_request("http://ip6-localhost:3004/command", "", $log);
	}
}

?>


<!DOCTYPE html>
<html>
<head>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<h1> RPITS 'Hacked Up' Animated Title Generator </h1>
<title>RPITS Animated Titles</title>
<p>Press Enter to get an inputbox, type player in box, press enter again. This will put an image in preview. After pressing 'a', hopefully an animated title will run. Otherwise, prepare for crash. Space for fade_out <br>Green box -> title in preview <br>Red box -> title on program (live)</p>
</head>
</html>

<script>
var playerarray = null;

jQuery.ajax({
	type: "GET",
	url: 'animHeads.php',
	dataType: 'json',
	data: {folderdata: "folderdata"},
	success: function(data){
		playerarray = data;
		playerarray.shift();
		playerarray.shift();
		playerarray.sort(function(a,b) {
			var r1 = a.match(/^\d+/);
			var r2 = b.match(/^\d+/);
			return r1[0] - r2[0];
		});
		//console.log(playerarray);
	}
})

var player = null;
function show_image(src, width, height, alt) {
    var img = document.createElement("img");
    img.src = src;
    img.width = width;
    img.height = height;
    img.alt = alt;
	img.id = "img";

    // This next line will just add it to the <body> tag
    document.body.appendChild(img);
}

window.onkeyup = function(e) {
	var key = e.keyCode ? e.keyCode : e.which;

	if (key == 13) { //enter
		var query = window.prompt("Player?")
		var found = false;
		
		if (query != null && query.trim() != ""){
			$.each(playerarray, function(index, value){
				if (value.toLowerCase().indexOf(query.toLowerCase()) > -1){
					player = value.replace(".png", "");
					found = true;
					return false;
				}
			});
			
			if (found) {
				//console.log(player);
				console.log("Successfully Loaded in: " + player);
				$("#img").remove();
				show_image("statscard_no_img/rpi-mh/" + player + ".png", 480, 270, player)
				$("#img").css("border", "5px solid #00ff00");
			}
			else {
				console.log("no match for " + query);
				player = null;
				$("#img").remove();
			}
		}
		else{
			console.log("Unsuccessful player load");
			$("#img").remove();
		}
	}
		
	 if (key == 67) { //c
		jQuery.ajax({
			type: "POST",
			url: 'animHeads.php',
			dataType: 'json',
			data: {fade_out: "cut"}
		})
			console.log("Cut: " + player);
			$("#img").remove();
			player = null;
		}
		
	
	if (key == 65) { //a
		if (player!= null){
				jQuery.ajax({
					type: "POST",
					url: 'animHeads.php',
					dataType: 'json',
					data: {player: player}
				});
			console.log("Animation playing for: " + player);
			$("#img").css("border", "5px solid #ff0000");
		}
		else{
			console.log("null player -> No animation");
		}
	}
	
	if (key == 32) { //space
		jQuery.ajax({
			type: "POST",
			url: 'animHeads.php',
			dataType: 'json',
			data: {fade_out: "fade_out"}
		})
		console.log("Fading out: " + player);
		player = null;
		console.log("Player -> " + player);
		$("#img").remove();
	}
}
</script>


