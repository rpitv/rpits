(function()
{
	window.renderQueue = {
    queue: [],
    
    addToQueue: function(tid) // Add a render job to the queue
    {
      if ($("#q"+tid).length == 0) // chech for duplicates
      { 
        var startNum = this.queue.push(tid); // Add title id to the queue
    	  $('#renderQueue').append('<span id="q'+ tid +'" class="queueItem"> ' + startNum + '. ' + tid +' </span>');
      }
      else if ($("#q"+tid).css("background-color") == "rgb(0, 255, 0)")
      {
        $("#q"+tid).remove();
        this.addToQueue(tid);
      }
    },

    processQueue: function(index) // Start rendering queue (single pass)
    {
      if (this.queue.length == 0) // Don't mess with an empty queue
      {
        alert("Render Queue is already complete!");
        return;
      }

    	$.ajax({	// Render something 
    		type: "GET",
    		url: "im_render_title.php?id="+this.queue[index],
        accepts: "image/png",
        async: false,
        timeout: 20000,
    		success: function(data) {
          $("#q"+this.queue[index]).css("background-color", "00FF00"); // Mark as green on list
          
          this.queue.splice(index,1); // Remove first element from queue (it is now done)
          
          // Check before recursively calling
          if ((this.queue.length != 0) && (this.queue.length > index))
          {
            this.processQueue(index);
          }
          
    		}.bind(renderQueue),
        error: function() {
    			$("#q"+tempTitle).css("background-color", "FF0000"); // Mark as red on list
          index += 1;
 
          // Check before recursively calling
          if ((this.queue.length != 0) && (this.queue.length > index))
          {
            this.processQueue(index);
          }
          
    		}.bind(renderQueue),
        complete: function() {
          //alert("completed");
        }
    	});
    },

    pruneQueue: function() // Remove finished jobs from list
    {
      $(".queueItem").each( function(i)
      {
        if ($(this).css("background-color") == "rgb(0, 255, 0)")
        {
          $(this).remove();
        };
      });
    },

    destroyQueue: function() // Erase queue without predjudice
    {
      if(confirm("Permanently remove all jobs?"))
      {
        this.queue.length = 0;
        $(".queueItem").each( function()
        {
          $(this).remove();
        });
      }
    }
	};
}());


$(window).on('beforeunload', function()
{
  return "WARNING: Leaving or reloading will mess up the queue.\n\nQueueing is very important in the titling system and the UK.\n";
});


$(document).ready( function()
{

});

