(function()
{
	window.renderQueue = {
    queue: [],
    process: 0,
    
    addToQueue: function(tid) // Add a render job to the queue
    {
      if (this.queue.length == 0) // Show queue if it was hidden
      {
        $("#renderQueue").fadeIn(400);
      }

      if ($("#q"+tid).length == 0) // check for duplicates
      { 
        var startNum = this.queue.push(tid); // Add title id to the queue
        var titleName = $("#"+tid).text(); // Get actual title for queue status box
    	  $('#renderQueue').append('<span id="q'+ tid +'" class="queueItem"><pre> ' + titleName +' </pre></span>');
      }
      else if ($("#q"+tid).css("background-color") == "rgb(0, 255, 0)")
      {
        $("#q"+tid).remove();
        this.addToQueue(tid);
      }
    },

    processQueue: function(index, recursive) // Start rendering queue (single pass)
    {
      if (this.queue.length == 0) // Don't mess with an empty queue
      {
        alert("Render Queue is already complete!");
        return;
      }
      else if ((process == 1) && (recursive == 0)) // If processing is happening when called from the UI...
      {
        process = 0; // Pause the processing (naive)
        $("#process").html("&#xe047;");
      }
      else
      {
        process = 1; // Processing starts
        $("#process").html("&#xe049;");
      }

      //alert("This allows the page to render, but there must be a better way.");

    	$.ajax({	// Render a title 
    		type: "GET",
    		url: "im_render_title.php?id="+this.queue[index],
        accepts: "image/png",
        async: true,
        timeout: 20000,
    		success: function(data) {
          $("#q"+this.queue[index]).fadeOut(400, function(){
            $(this).css("background-color", "00FF00"); // Mark as green on the list
          });
          $("#q"+this.queue[index]).fadeIn(400);
          
          this.queue.splice(index,1); // Remove first element from queue (it is now done)
          }.bind(renderQueue),
        error: function() {
    			$("#q"+this.queue[index]).css("background-color", "FF0000"); // Mark as red on list
          index += 1;
    		}.bind(renderQueue),
        complete: function() {
          // Check before recursively calling
          if ((this.queue.length != 0) && (this.queue.length > index) && (process == 1))
          {
            setTimeout(this.processQueue(index, 1));
          }
          else
          {
            process = 0; // Processing has ended
            $("#process").html("&#xe047;");
          }
        }.bind(renderQueue)
    	});
    },

    pruneQueue: function() // Remove finished jobs from list
    {
      if (this.queue.length == 0)
      {
        $("#renderQueue").fadeOut(400); // Hide empty queue
      }
      $(".queueItem").each( function(i)
      {
        if ($(this).css("background-color") == "rgb(0, 255, 0)")
        {
          $(this).fadeOut(400, function(){
            $(this).remove();
          });
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
        $("#renderQueue").fadeOut(400); // Hide empty queue
      }
    }
	};
}());


$(window).on('beforeunload', function()
{
  if (eventIdNum) { // Only warn when in the LIVE UI
    return "WARNING: Leaving or reloading will mess up the queue.\n\nQueueing is very important in the titling system and the UK.\n";
  }
});


$(document).ready( function()
{
  $("#renderQueue").hide();
});

