(function()
{
	window.renderQueue = {
        queue: [],
    
        addToQueue: function(tid)
        {
    		this.queue.push(tid); // Add title id to the queue
    		$('#list').append('<br/><span id="'+ tid +'"> '+ tid +' </span>');
    	},
    
    	processQueue: function()
        {
    		while ( this.queue.length() > 0 )
    		{
    			$.ajax({	// Render something
    				type: "GET",
    				url: "im_render_title.php?id="+this.queue[0],
    				success: function(data) {
    					this.renderQueue.shift(); // Remove first element from queue (it is now done)
    					document.getElementById(this.queue[0]).bgColor="#00FF00"; // Mark as green on list			
    				}
    			});
    		}
    	}
	};
    
    
	
}());