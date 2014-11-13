(function() { window.renderQueue = {
	queue: [],
	processing: 0,
	
	addToQueue: function(title, bustCache, startFlag) {
	// Add a render job to the queue
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
		startFlag = startFlag || false;

		if (this.queue.length == 0) { // Show queue if it was hidden
			$("#renderQueue").fadeIn(400);
		}
		
		if ($("#q"+title.id).length == 0) { // check for duplicates
			this.queue.push({ title:title, bustCache:bustCache }); // Add title id to the queue
			$('#renderQueue').append('<div id="q'+ title.id +'" class="queueItem waiting"><div class="queueItemButton" onclick="window.renderQueue.removeFromQueue(' + title.id + ')">&#x2713;</div><div class="queueItemButton" onclick="window.renderQueue.moveInQueue(0, '+ title.id +')">&#xe043;</div><pre> ' + title.getDisplayName() + '</pre></div>');
			if (startFlag == true) {
				this.processQueue(true);
			}
		} else if ($("#q"+title.id).hasClass("completed")) {
			$("#q"+title.id).remove();
			this.addToQueue(title, bustCache, startFlag);
		}
	},

	removeFromQueue: function(castaway) {
	// Remove a single item from the queue
		if ($("#q"+castaway).hasClass("pending")){
			return; // don't remove titles being rendered
		}
		
		var index = $(".pending, .waiting").index($("#q"+castaway));

		if (!this.queue[index]) { // See if it is even there
			this.pruneQueue(); // prune queue if not
			return;
		}

		var tempID = this.queue[index].title.id;
		this.queue.splice(index, 1);

		$("#q"+tempID).fadeOut(400, function() {
			$("#q"+tempID).remove();
			if (renderQueue.queue.length == 0) {
				$("#renderQueue").fadeOut(400); // Hide empty queue
			}
		});
	},

	moveInQueue: function(destination, traveler) {
	// Move an item in the queue
		if ($(".waiting").length < 2) {
			return; // nowhere to move
		}

		if (this.processing == 1) {
			$("#q"+traveler+" .queueItemButton").last().css("background", "red");
			setTimeout(	function(){$("#q"+traveler+" .queueItemButton").last().css("background", "orange")}, 400 );
			return; // don't move in moving queue
		}

		var index = $(".pending, .waiting").index($("#q"+traveler));
		var tempTraveler = this.queue.splice(index, 1); // remove traveler

		if ($("#q"+this.queue[destination].title.id).hasClass("pending")){
			destination++; // move to non-pending title
		}

		this.queue.splice(destination, 0, tempTraveler[0]);

		$("#q"+tempTraveler[0].title.id).fadeOut(400, function() {
			$(this).insertBefore( $("#q" + renderQueue.queue[destination+1].title.id) );
			$(this).fadeIn(400, function() {
				
			});
		});
	},

	processQueue: function(startSoft, recursive, index) {
	// Start rendering queue (single pass)
		startSoft = startSoft || false;
		recursive = recursive || false;
		index = index || 0;

		if ((this.queue.length == 0) && (recursive == false)) { // don't start empty queue
			alert("Render Queue is already complete!");
			return;
		} else if ((this.processing == 1) && (recursive == false)) { // pause condition
			if (startSoft == true) {
				return; // ignore this call if queue is already running
			}

			this.processing = 0; // Pause the processing (sending new jobs)
			$("#process div").html("&#xe047;"); // Play Icon
			return;
		} else {
			this.processing = 1; // Processing starts
			$("#process div").html("&#xe049;"); // Pause Icon
		}

		var bustCache = this.queue[index].bustCache ? '&bustCache=true' : '';
		var url_str = this.queue[index].title.getRenderURL() + bustCache;

		$("#q"+this.queue[index].title.id).fadeOut(400, function() {
			$(this).removeClass("waiting");
			$(this).addClass("pending");
			$(this).fadeIn(400, function() {

				$.ajax( {	// Render a title 
					type: "GET",
					url: url_str,
					accepts: "image/png",
					async: true,
					timeout: 20000,
					success: function(data) {
						$("#q"+this.queue[index].title.id).fadeOut(400, function() {
							$(this).removeClass("pending");
							$(this).addClass("completed");
							$(this).fadeIn(400, function() {
								renderQueue.queue.splice(index,1); // Remove this element from queue (it is now done)
								if ((renderQueue.queue.length == 0) || (index >= renderQueue.queue.length)) {
									renderQueue.processing = 0; // Processing has ended
									$("#process div").html("&#xe047;"); // Play Icon
									renderQueue.pruneQueue();
								} else if (renderQueue.processing == 1) {
									renderQueue.processQueue(false, true);
								} else {
									renderQueue.processing = 0;
								}
							});
						});
					}.bind(renderQueue),
					error: function() {
						$("#q"+this.queue[index].title.id).addClass("pending");
						$("#q"+this.queue[index].title.id).addClass("failed");
						index += 1;
						this.processQueue(false, true);
					}.bind(renderQueue)
				});

			});
		});
	},

	pruneQueue: function(noHide) {
	// Remove finished jobs from list
		$(".queueItem").each( function(i) {
			if ($(this).hasClass("completed")) {
				$(this).fadeOut(400, function() {
					$(this).remove();
					if ((renderQueue.queue.length == 0) && (noHide != 1)) {
						$("#renderQueue").fadeOut(400); // Hide empty queue
					}
				});
			};
		});
	},

	destroyQueue: function() {
	// Erase queue without predjudice
		if (this.queue.length == 0) {
			this.pruneQueue();
		} else if (confirm("Permanently remove all jobs?")) {
			this.queue.length = 0;
			$(".queueItem").each( function() {
				$(this).remove();
			});
			$("#renderQueue").fadeOut(402); // Hide empty queue
		}
    }
}; }());

function scoreTitleUpdate(homeScore, awayScore, scoreTitleId) {
// Auto-queue the lower third title on score change
	var url = "/scoreboard/";
	homeScore = (homeScore > -1) ? homeScore : -1;
	awayScore = (awayScore > -1) ? awayScore : -1;

	if (!scoreTitleId) {
		$("#pane ul li").each( function() {
			if ($(this).text().indexOf("Score Lower Third") >= 0) {
				scoreTitleId = $(this).data('title').id;
				return;
			}
		});
		scoreTitleId = (scoreTitleId >= 0) ? scoreTitleId : -1;
	}

	if (scoreTitleId == -1) {
		return;	
	}

	$.getJSON(url+"data.json", function(data) {
		if ((parseInt(data.home.score) != parseInt(homeScore)) ||
			(parseInt(data.away.score) != parseInt(awayScore)) ||
			((data.clock.period_remaining < 300)&&(data.clock.period_remaining != 0))) {
			$.ajax({
				type: "POST",
				async: false,
				url: "cdb_update.php",
				data: scoreTitleId + "=hScore&text=" + data.home.score,
				success: function() {
					homeScore = data.home.score;
					$.ajax({
						type: "POST",
						async: false,
						url: "cdb_update.php",
						data: scoreTitleId + "=vScore&text=" + data.away.score,
						success: function() {
							awayScore = data.away.score;
							if ((data.clock.period_remaining > 300) ||
								(data.clock.period_remaining == 0)) {
								window.renderQueue.addToQueue(scoreTitleId, true, true);
								return;
							}

							$.ajax({
								type: "POST",
								async: false,
								url: "cdb_update.php",
								data: scoreTitleId + "=gameStatus&text=" + data.periodDescription.end_of_period,
								success: function() {
									window.renderQueue.addToQueue(scoreTitleId, true, true);
								}
							});
						}
					});
				}
			});
		}
	}).fail(function(d){
		return; // quit if no scoreboard feed
	});
	setTimeout(function(){ scoreTitleUpdate(homeScore, awayScore, scoreTitleId); }, 10000);
};


$(window).on('beforeunload', function() {
	if ((ui.eventId) && (parseInt(window.renderQueue.queue.length) != 0)) { // Only in the LIVE UI on empty queue
		return "WARNING: Leaving or reloading will mess up the queue.\n\nQueueing is very important in the titling system and the UK.\n";
	}
});


$(document).ready( function() {
	if (ui.eventId) { // Only in the LIVE UI
		setTimeout(function(){ scoreTitleUpdate(); }, 1000); // Start auto score update
	}
});

