// THIS FILE IS HOPEFULLY NO LONGER A TAB CHARACTER DISASTER

(function() { window.renderQueue = {
	queue: [],
	process: 0,
	
	////////////////////////////////////////////////////////////////////////////
    addToQueue: function(title, bustCache) { // Add a render job to the queue //
		if (!(title instanceof RPITS.ui.Title)) {
			var i = 0,foundTitle;
			for (var key in ui.tabs.lists) {
				foundTitle = ui.tabs.lists[key].getTitleById(title);
				if (foundTitle) {
					title = foundTitle;
					break;
				}
			}
		}

		console.log(title);
		bustCache = bustCache || false;

		if (this.queue.length == 0) { // Show queue if it was hidden
			$("#renderQueue").fadeIn(400);
		}
      
		if ($("#q"+title.id).length == 0) { // check for duplicates
			this.queue.push({ title:title, bustCache:bustCache }); // Add title id to the queue
			$('#renderQueue').append('<div id="q'+ title.id +'" class="queueItem"><div class="queueItemButton" onclick="window.renderQueue.removeFromQueue(' + title.id + ')">&#x2713;</div><div class="queueItemButton" onclick="window.renderQueue.moveInQueue(0, '+ title.id +')">&#xe043;</div><pre> ' + title.getDisplayName() + '</pre></div>');
		} else if ($("#q"+title.id).css("background-color") == "rgb(0, 255, 0)") {
			$("#q"+title.id).remove();
			this.addToQueue(title,bustCache);
		}
	},
    
	///////////////////////////////////////////////////////////////////////////////
	removeFromQueue: function(castaway) { // Remove a single item from the queue //
		var ttype = $("#"+castaway).attr("type");
		var index, i;
		//var index = this.queue.indexOf({'id':castaway, 'type':ttype});
		for (i=0; i < this.queue.length; i++) { // I know this is the nieve way...
			if (this.queue[i].title.id == castaway) {
				index = i;
			}
		}

		if (!this.queue[index]) { // See if it is even there
			this.pruneQueue(); // prune queue if not
			return;
		}

		var tempID = this.queue[index].title.id;
		this.queue.splice(index, 1);

		if (this.queue.length == 0) {
			setTimeout('$("#renderQueue").fadeOut(400)', 401); // Hide empty queue
		}

		$("#q"+tempID).fadeOut(400, function(){
			$("#q"+tempID).remove();
		});
	},

	//////////////////////////////////////////////////////////////////////////////
	moveInQueue: function(destination, traveler) { // Move an item in the queue //
		var ttype = $("#"+traveler).attr("type");

		var startIndex, i, index;
		//var index = this.queue.indexOf({'id':castaway, 'type':ttype});
		for (i=0; i < this.queue.length; i++) { // I know this is the nieve way...
			if (this.queue[i].title.id == traveler) {
				index = i;
				break;
			}
		}

		var tempTraveler = this.queue.splice(index, 1); // remove traveler
		this.queue.splice(destination, 0, tempTraveler[0]);

		var tempName = this.queue[destination+1].title.id;

		$("#q"+tempTraveler[0].title.id).fadeOut(400, function(){
			$(this).insertBefore( $("#q"+tempName) );
			$(this).fadeIn(400);
		});

	},

	////////////////////////////////////////////////////////////////////////////////////
	processQueue: function(index, recursive) { // Start rendering queue (single pass) //
		index = index ? index : 0;

		if (this.queue.length == 0) { // Don't mess with an empty queue
			alert("Render Queue is already complete!");
			return;
		} else if ((process == 1) && (recursive == 0)) { // If processing is happening when called from the UI...
			process = 0; // Pause the processing (naive)
			$("#process").html("&#xe047;"); // Play Icon
		} else {
			process = 1; // Processing starts
			$("#process").html("&#xe049;"); // Pause Icon
		}

		var bustCache = this.queue[index].bustCache ? '&bustCache=true' : '';
		var url_str = this.queue[index].title.getRenderURL() + bustCache;

		$.ajax({	// Render a title 
			type: "GET",
			url: url_str,
			accepts: "image/png",
			async: true,
			timeout: 20000,
			success: function(data) {
				$("#q"+this.queue[index].title.id).fadeOut(400, function() {
					$(this).css("background-color", "00FF00"); // Mark as green on the list
				});
				$("#q"+this.queue[index].title.id).fadeIn(400);

				this.queue.splice(index,1); // Remove first element from queue (it is now done)
			}.bind(renderQueue),
			error: function() {
				$("#q"+this.queue[index].title.id).css("background-color", "FF0000"); // Mark as red on list
				index += 1;
			}.bind(renderQueue),
			complete: function() {
				// Check before recursively calling
				if ((this.queue.length != 0) && (this.queue.length > index) && (process == 1)) {
					setTimeout(this.processQueue(index, 1));
				} else {
					process = 0; // Processing has ended
					setTimeout('$("#process").html("&#xe047;")', 801); // Play Icon (timed after color updates)
					setTimeout( 'renderQueue.pruneQueue()' , 408); // Prune when finished
				}
			}.bind(renderQueue)
		});
	},

	/////////////////////////////////////////////////////////////
	pruneQueue: function() { // Remove finished jobs from list //
		if (this.queue.length == 0) {
			setTimeout('$("#renderQueue").fadeOut(400)', 401); // Hide empty queue
		}
		$(".queueItem").each( function(i) {
			if ($(this).css("background-color") == "rgb(0, 255, 0)") {
				$(this).fadeOut(400, function(){
					$(this).remove();
				});
			};
		});
	},

	///////////////////////////////////////////////////////////////
	destroyQueue: function() { // Erase queue without predjudice //
		if (this.queue.length == 0) { // Don't bother prompting if done
			this.pruneQueue();
		} else if(confirm("Permanently remove all jobs?")) {
			this.queue.length = 0;
			$(".queueItem").each( function() {
				$(this).remove();
			});
			$("#renderQueue").fadeOut(402); // Hide empty queue
		}
    }
}; }());

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function scoreTitleUpdate(homeTeamScore, awayTeamScore, gameStatus) { // Auto-queue the lower third title on score change //

	var url = "/scoreboard/";
	var tempHome = -1;
	var tempAway = -1;
	var scoreTitleId = -1;

	$("#pane ul li").each( function() {
		if ($(this).text().indexOf("Score Lower Third") > 0) {
			scoreTitleId = $(this).data('title').id;
			return;
		}
	});

	var tempStatus = "Current Score"
	var updateStatus = scoreTitleId + "=gameStatus&text=" + tempStatus;

	$.getJSON(url+"team/1", function(data) { // 1 is home
		$.each(data, function(key, value) {
			if (key == "score") {
				if (parseInt(homeTeamScore) == -9001) {
					homeTeamScore = value;
					return;
				}

				tempHome = value;
				var updateHome = scoreTitleId + "=hScore&text=" + tempHome;

				if (parseInt(tempHome) != parseInt(homeTeamScore)) {
					$.ajax({
						type: "POST",
						async: false,
						url: "cdb_update.php",
						data: updateHome,
						success: function() {
							homeTeamScore = tempHome;
							$.ajax({
								type: "POST",
								url: "cdb_update.php",
								data: updateStatus,
								success: function() {
									window.renderQueue.addToQueue( scoreTitleId );
								}
							});
						}
					});
				}

				return;
			}
		});
	});

	$.getJSON(url+"team/0", function(data) { // 0 is away
		$.each(data, function(key, value) {
			if (key == "score") {
				if (parseInt(awayTeamScore) == -9001) {
					awayTeamScore = value;
					return;
				}

				tempAway = value;
				var updateAway = scoreTitleId + "=vScore&text=" + tempAway;

				if (parseInt(tempAway) != parseInt(awayTeamScore)) {
					$.ajax({
						type: "POST",
						async: false,
						url: "cdb_update.php",
						data: updateAway,
						success: function() {
							awayTeamScore = tempAway;
							$.ajax({
								type: "POST",
								url: "cdb_update.php",
								data: updateStatus,
								success: function() {
									window.renderQueue.addToQueue( ScoreTitleId );
								}
							});
						}
					});
				}
			
				return;
			}
		});
	});

	setTimeout(function(){ scoreTitleUpdate(homeTeamScore, awayTeamScore, gameStatus); }, 10000);
};


$(window).on('beforeunload', function() {
	if ((ui.eventId) && (parseInt(window.renderQueue.queue.length) != 0)) { // Only in the LIVE UI on empty queue
		return "WARNING: Leaving or reloading will mess up the queue.\n\nQueueing is very important in the titling system and the UK.\n";
	}
});


$(document).ready( function() {
	$("#renderQueue").hide(); // Hide queue status box until it is needed.

	if (ui.eventId) { // Only in the LIVE UI
		setTimeout(function(){scoreTitleUpdate( -9001, -9001, "Current Score")}, 1000); // Start auto score update
	}
});

