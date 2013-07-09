(function()
{
	window.renderQueue = {
    queue: [],
    process: 0,
    
    /////////////////////////////////////////////////////////////
    addToQueue: function(tid) // Add a render job to the queue //
    {
      if (this.queue.length == 0) // Show queue if it was hidden
      {
        $("#renderQueue").fadeIn(400);
      }

      if ($("#q"+tid).length == 0) // check for duplicates
      { 
        var startNum = this.queue.push(tid); // Add title id to the queue
        var titleName = $("#"+tid).text(); // Get actual title for queue status box
    	  $('#renderQueue').append('<div id="q'+ tid +'" class="queueItem"><div class="queueItemButton" onclick="window.renderQueue.removeFromQueue(' + tid + ')">&#xe05a;</div><div class="queueItemButton" onclick="window.renderQueue.moveInQueue(0, '+ tid +')">&#xe043;</div><pre> ' + titleName + '</pre></div>');
      }
      else if ($("#q"+tid).css("background-color") == "rgb(0, 255, 0)")
      {
        $("#q"+tid).remove();
        this.addToQueue(tid);
      }
    },
    
    /////////////////////////////////////////////////////////////////////////////
    removeFromQueue: function(castaway) // Remove a single item from the queue //
    {
      var index = this.queue.indexOf(castaway);

      if (!this.queue[index]) // See if it is even there
      {
        this.pruneQueue(); // prune queue if not
        return;
      }

      var tempID = this.queue[index];
      this.queue.splice(index, 1);

      if (this.queue.length == 0)
      {
        setTimeout('$("#renderQueue").fadeOut(400)', 401); // Hide empty queue
      }

      $("#q"+tempID).fadeOut(400, function(){
        $("#q"+tempID).remove();
      });

    },

    ////////////////////////////////////////////////////////////////////////////
    moveInQueue: function(destination, traveler) // Move an item in the queue //
    {
      var startIndex = this.queue.indexOf(traveler);

      var tempItem = this.queue.splice(startIndex,1);
      var tempName = this.queue[destination];

      //alert(tempItem[0]);
      //alert(tempName);

      $("#q"+tempItem[0]).fadeOut(400, function(){
        $(this).insertBefore( $("#q"+tempName) );
        $(this).fadeIn(400);
      });
      
      this.queue.splice(destination, 0, tempItem[0]);

      //alert(this.queue[destination]);
    },

    //////////////////////////////////////////////////////////////////////////////////
    processQueue: function(index, recursive) // Start rendering queue (single pass) //
    {
      if (this.queue.length == 0) // Don't mess with an empty queue
      {
        alert("Render Queue is already complete!");
        return;
      }
      else if ((process == 1) && (recursive == 0)) // If processing is happening when called from the UI...
      {
        process = 0; // Pause the processing (naive)
        $("#process").html("&#xe047;"); // Play Icon
      }
      else
      {
        process = 1; // Processing starts
        $("#process").html("&#xe049;"); // Pause Icon
      }

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
            setTimeout('$("#process").html("&#xe047;")', 801); // Play Icon (timed after color updates)
          }
        }.bind(renderQueue)
    	});
    },

    ///////////////////////////////////////////////////////////
    pruneQueue: function() // Remove finished jobs from list //
    {
      if (this.queue.length == 0)
      {
        setTimeout('$("#renderQueue").fadeOut(400)', 401); // Hide empty queue
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

    /////////////////////////////////////////////////////////////
    destroyQueue: function() // Erase queue without predjudice //
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
  $("#renderQueue").hide(); // Hide queue status box until it is needed.
});

