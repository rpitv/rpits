<script src="renderQueue.js" type="text/javascript"></script>

<?php
include("include.php");

$tid = $_POST[$tid]; // Posted id (to add to queue)

$id = $_GET["id"]; // Get id (to return status on)

// if($_GET['varName']) { GET'd } else {POST'd}
// test to see if the get var you want exists

?>

<br style="clear:both" />
<h1>Queue:</h1>
<p id="list"></p>

<script type="text/javascript">

function addToQueue(){
	renderQueue.push("<?= $tid ?>"); // Add title id to the queue
	$('#list').innerHTML = $('#list').innerHTML += '<br/><span tid="<?= $tid ?>"> <?= $tid ?> </span>';
};

function processQueue(){
	while ( renderQueue.length() > 0 )
	{
		$.ajax({	// Render something
			type: "GET",
			url: "im_render_title.php?id="+renderQueue[0],
			success: function(data) {
				renderQueue.shift(); // Remove first element from queue (it is now done)
				document.getElementById(renderQueue[0]).bgColor="#00FF00"; // Mark as green on list			
			}
		});
	}
};

</script>