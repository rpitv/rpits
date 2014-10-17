// Pass in CHS Stat Table and player object
function parse_CHS_for_player(stats, p) {  
	// skaters
	var i = 4;
	for (i; i<stats.length; i++) {
		// find the current player (hacky)
		if (parseInt(stats[i][0]+stats[i][1]) == p.number) {

			if (!p.stype) { p.stype = 'ho'; } else { continue; }

			var pstats = stats[i].split(" | ")[1];
			pstats = pstats.replace(/  +/g, ' ').split(' ');

			p.s1 = pstats[0];
			p.s2 = pstats[1];
			p.s3 = pstats[2];
			p.s4 = pstats[3];
			if (pstats.length == 10) { // penalty minutes
				p.s5 = pstats[5];
			} else {
				p.s5 = 0;
			}
			p.s6 = '';

			break;
		}
		if (stats[i][0]=="-") { break; }
	}

	if (p.stype == 'ho') { return; }

	// goalies
	var j = i + 5;
	for (j;j<i+11;j++) {
		if (!$.isNumeric((stats[j][0])+stats[j][1])){ break; }

		if (parseInt(stats[j][0]+stats[j][1]) == p.number) {
			var pstats = stats[j].split(" | ")[1];
			pstats = pstats.trim().replace(/  +/g, ' ').split(' ');

			p.s1 = pstats[0];

			// record
			var record = pstats[7]+pstats[8]+pstats[9];
			if (record.length > 8) {
				record = record.slice(0,record.indexOf(pstats[pstats.length-4][0]));
			}
			record = record.split('-');
			p.s2 = record[0];
			p.s3 = record[1];
			p.s4 = record[2];

			p.s5 = pstats[5];
			p.s6 = pstats[6];
		}
	}
}

// Pass in the table HTML, and the number of initial rows not containing data
function parse_table_HTML(table_HTML, stats, rowsToSkip) {
	if (rowsToSkip === undefined) {
		rowsToSkip = 1; // assume 1 header row
	}

	// Sanitize nbsp weirdness - or NOT because it is useful for names
	table_HTML = table_HTML.replace(/\&nbsp\;/g, '|' );

	$('#rosterTable').html(table_HTML);
	$('#rosterTable table').css('font-size', '8pt');
	$('#tableEntry').hide();
	$('#showTableEntry').show();

	var num_players = 0;
	var num_rows = 0;
	var submission_string = '';
	var temp1 = '';
	var temp2 = '';

	$('#rosterTable tr').slice(rowsToSkip).each(function() {
		var player = new Object();
		num_players++;
		num_rows = $(this).find('td').length;

		$(this).find('td').each(function(index) {
			switch (index) {
				case 0: // parse number
					player.number = $(this).text().trim().replace(/\#/g, '');
					break;
				case 1: // parse FIRST and LAST name and DRAFT
					temp1 = $(this).text().trim().replace("\'", "\\\'");

					temp2 = temp1.split('|');
					player.first_name = temp2.shift();  // get FIRST name(s)

					if (temp2[temp2.length-1].indexOf('(') >= 0) {  // get (DRAFT) out
						temp1 = temp2.pop();
						player.draft_pick = temp1.substr(1,3);
					}
					
					player.last_name = temp2.join(" ").trim();
					break;
				case 2: // parse year
					temp1 = $(this).text();
					temp2 = temp1.charAt(1).toLowerCase(); 
					player.year = temp1.charAt(0) + temp2;
					break;
				case 3: // parse position
					player.position = $(this).text().trim();
					if (player.position == "G") { player.stype = "hg"; }
					break;
				case 4: // parse height
					player.height = $(this).text().trim();
					break;
				case 5: // parse weight (M)
					if (num_rows == 9) {
						player.weight = $(this).text().trim();
					} else { //handedness (W)
						//player.hand = $(this).text().trim();
					}
					break;
				case 6: // parse handedness (M), age (W)

					break;
				case 7: // parse age (M), hometown + etc. (W)
					if (num_rows == 8) {
						player.hometown = sanitizeHometown($(this).text().trim().split(' / ')[0]);
					}
					break;
				case 8: // parse hometown and prev team (M)
					player.hometown = sanitizeHometown($(this).text().trim().split(' / ')[0]);
					break;
			}
		});

		parse_CHS_for_player(stats, player);

		submission_string += buildSubmissionLine(player);
	});

	$('#csv_textarea').val(submission_string.trim());
}

function buildSubmissionLine(player) {
	var str = '';

	if (player.number) { str += player.number + '|'; } else { str += '|'; }

	str += player.first_name + '|' + player.last_name + '|';
	
	if (player.position) { str += player.position + '|'; } else { str += '|'; }
	if (player.height) { str += player.height + '|'; } else { str += '|'; }
	if (player.weight) { str += player.weight + '|'; } else { str += '|'; }
	if (player.year) { str += player.year + '|'; } else { str += '|'; }
	if (player.hometown) { str += player.hometown + '|'; } else { str += '|'; }
	if (player.stype) { str += player.stype + '|'; } else { str += '|'; }

	for (var z = 1; z<=6; z++) {
		if (player['s'+z]) { str += player['s'+z]; }
		str += '|';
	}

	if (player.draft_pick) { str += player.draft_pick; }

	return str + '\n';
}

function sanitizeHometown(place) {
	place = place.replace("\'", "\\\'");

	if (place.indexOf(",")<0) { return place; }

	var temp = place.split(",");
	var state = temp.pop().trim();
	var city = temp.join().trim();

	var state2 = state.replace(/\./g, "");

	if (state2.length <= 2) {
		state = state2.toUpperCase();
	} else if (state2 == getAbbr(state2)) {
		// keep state as-is, even with '.' at end
	} else {
		state = getAbbr(state2);
	}

	return city + ", " + state;
}

function parseRosterSIDEARM(table_HTML, rowsToSkip) {
	if (rowsToSkip === undefined) {
		rowsToSkip = 1; // assume 1 header row
	}

	// Sanitize nbsp weirdness
	table_HTML = table_HTML.replace(/\&nbsp\;/g, '' );

	$('#rosterSIDEARM').html(table_HTML);
	$('#rosterSIDEARM table').css('font-size', '8pt');
	$('#boxSIDEARM').hide();

	c = discoverColumnsSIDEARM();

	var num_players = 0;
	var num_rows = 0;
	var submission_string = '';
	var temp1 = '';
	var temp2 = '';

	$('#rosterSIDEARM tr').slice(rowsToSkip).each(function() {
		var player = new Object();
		num_players++;
		num_rows = $(this).find('td').length;

		$(this).find('td').each(function(index) {
			switch (index) {
				case c.number: // parse number
					player.number = $(this).text().trim().replace(/\#/g, '');
					break;
				case c.name: // parse FIRST and LAST name
					temp1 = $(this).text().trim().replace("\'", "\\\'");

					temp2 = temp1.split(' ');
					player.first_name = temp2.shift();  // get FIRST name (assume 1 first name)
					player.last_name = temp2.join(" ").trim();
					break;
				case c.year: // parse year
					temp1 = $(this).text().trim().toLowerCase();
					if (temp1.indexOf("fr")>=0) {
						temp2 = "Fr";
					} else if (temp1.indexOf("so")>=0) {
						temp2 = "So";
					} else if (temp1.indexOf("jr")>=0) {
						temp2 = "Jr";
					} else if (temp1.indexOf("sr")>=0) {
						temp2 = "Sr";
					} else {
						temp2 = $(this).text().trim();
					}
					player.year = temp2;
					break;
				case c.position: // parse position
					player.position = $(this).text().trim();
					break;
				case c.height: // parse height
					player.height = $(this).text().trim();
					break;
				case c.weight: // parse weight
					player.weight = $(this).text().trim();
					break;
				case c.hometown: // parse hometown isolated
					player.hometown = sanitizeHometown($(this).text().trim());
					break;
				case c.hometown_combined: // parse hometown from combined
					player.hometown = sanitizeHometown($(this).text().trim().split(' / ')[0]);
					break;
			}
		});

		submission_string += buildSubmissionLine(player);
	});

	$('#csv_textarea').val(submission_string.trim());
}

function discoverColumnsSIDEARM() { // Find column order from SIDEARM HTML
	var cols = new Object();
	var index = 0;

	$("#other_page .default_dgrd_header th").each(function() {
		if ($(this).hasClass('roster_dgrd_header_no')) {
			cols.number = index;
		} else if ($(this).hasClass('roster_dgrd_header_full_name')) {
			cols.name = index;
		} else if ($(this).hasClass('roster_dgrd_header_rp_position_short')) {
			cols.position = index;
		} else if ($(this).hasClass('roster_dgrd_header_rp_position_long')) {
			cols.position = index;
		} else if ($(this).hasClass('roster_dgrd_header_height')) {
			cols.height = index;
		} else if ($(this).hasClass('roster_dgrd_header_rp_weight')) {
			cols.weight = index;
		} else if ($(this).hasClass('roster_dgrd_header_academic_year')) {
			cols.year = index;
		} else if ($(this).hasClass('roster_dgrd_header_player_hometown')) {
			cols.hometown = index;
		} else if ($(this).hasClass('roster_dgrd_player_hometown')) {
			cols.hometown = index;
		} else if ($(this).hasClass('roster_dgrd_header_highschool')) {
			cols.highschool = index;
		} else if ($(this).hasClass('roster_dgrd_header_hometownhighschool')) {
			cols.hometown_combined = index;
		} else if ($(this).hasClass('roster_dgrd_header_rp_captain')) {
			cols.captain = index;
		} else if ($(this).hasClass('roster_dgrd_header_player_major')) {
			cols.major = index;
		} else if ($(this).hasClass('roster_dgrd_header_player_previous_school')) {
			cols.prev_school = index;
		}
		index++;
	});

	return cols;
}
	
function validateFinalSubmission() {
	if (($('#team_box').val()=="")||($('#team_box').val()==null)) {
		alert('Please enter a team name for the player(s).');
		$('#team_box').select();
		return false;
	} else if ($('#team_box').val().indexOf('-') < 0) {
		alert('Please follow the team name format "organization-team".');
		$('#team_box').select();
		return false;
	}
}
