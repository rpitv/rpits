(function()
{
	window.renderQueue = {
    queue: [],
    
    addToQueue: function(tid) // Add a render job to the queue
    {
      var startNum = this.queue.push(tid); // Add title id to the queue
    	$('#renderQueue').append('<span id="'+ tid +'" class="queueItem"> ' + startNum + '. ' + tid +' </span>');
    },
   
    //processJob: function()
    //{
      
    //},

    processQueue: function(index) // Start rendering queue (single pass)
    {
      //var oldLen = this.queue.length;
      //var processedIndex = 0;
      //alert("called process");
      //for( var i=0; i<oldLen; i++ ) 
    	//{
        //alert("processing element at "+ processedIndex  +" number " + i + "tid: " + this.queue[processedIndex] );
        alert("processing element at "+ index + "tid: " + this.queue[index] );

    		$.ajax({	// Render something 
    			type: "GET",
    			url: "im_render_title.php?id="+this.queue[index],
    			success: function(data) {
    				//this.renderQueue.shift(); // Remove first element from queue (it is now done)
     				
            document.getElementById(this.queue[index]).bgColor="#00FF00"; // Mark as green on list
            this.renderQueue.splice(index,1); // Remove first element from queue (it is now done)

            alert("win");
            //
            processQueue(index);
            //
    			},
          failure: function() {
    				document.getElementById(this.queue[index]).bgColor="#FF0000"; // Mark as red on list
            index += 1;
            alert("fail");
            //
            processQueue(index);
            //
    			}

    		});

        alert("wut");
    	//}
    },

    pruneQueue: function() // Remove finished jobs from list
    {
      $(".queueItem").each( function(i)
      {
        alert($(this).css("background-color"));
        if ($(this).css("background-color") == "rgb(0, 255, 0)")
        {
          $(this).remove();
        };
      });
    },

    destroyQueue: function() // Erase queue without predjudice
    {
      this.queue.length = 0;
    }
	};
}());


$(window).on('beforeunload', function()
{
  return "WARNING: Leaving or reloading will mess up the queue.\n\nQueueing is very important to the titling system and in the UK.\n";
});


$(document).ready( function()
{

});

