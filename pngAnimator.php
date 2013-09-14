<script src="js/lib/jquery-1.8.3.js" type="text/javascript"></script>

Filename: <input id="filename" type="text" value="anim/bar"/>
Ext: <input id="ext" type="text" value=".png"/>
Start: <input id="start" type="text" value="0"/>
End: <input id="end" type="text" value="59"/>
<button id="load">Load</button>
<button id="play">Play</button>
<div id="canvas"></div>

<script type="text/javascript">

$(document).ready(function() {

	console.log('...');

	var currentFrame = 0;
	var endFrame = 0;
	var start = 0;
	var end = 0;

	function pad(n, width, z) {
		z = z || '0';
		n = n + '';
		return n.length >= width ? n : new Array(width - n.length + 1).join(z) + n;
	}

	function nextImage() {
		if(currentFrame < endFrame) {
			$('#frame' + currentFrame).hide();
			currentFrame++;
			$('#frame' + currentFrame).show();
			requestAnimationFrame(nextImage);
		}
	}

	$('#load').on('click',function() {
		console.log('fuck you');
		start = $('#start').val();
		end = $('#end').val();
		var file = $('#filename').val();
		var ext = $('#ext').val();
		var imgs = [];
		$('#canvas').empty();
		for(var i = start; i <= end; i++) {
			var number = ("00"+i).slice(-3);
			var filename = file + number + ext;
			$('#canvas').append('<img src="'+filename+'" id="frame'+i+'" style="display:none"/>');
		}
		$('#frame0').show();
		currentFrame = start;
		endFrame = end;
	});
	$('#play').on('click',function(){
		currentFrame = start;
		endFrame = end;
		$('#canvas > img').hide();
		requestAnimationFrame(nextImage);
	});

});
</script>