function(){
	var window.renderQueue = new Array();
	
	renderQueue.methodName = function addToQueue(){
		renderQueue.push("<?= $tid ?>"); // Add title id to the queue
		$('#list').innerHTML = $('#list').innerHTML += '<br/><span tid="<?= $tid ?>"> <?= $tid ?> </span>';
	};

	renderQueue.function processQueue(){
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
	
}());