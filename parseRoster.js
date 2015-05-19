// Pass in CHS Stat Table and player object
/// TODO: Switch this over the the team object format from CHWT
/// TODO: Switch this to parse the full stats (with +/-)
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

	var players = [];
	var num_players = 0;
	var num_rows = $('#rosterTable tr').slice(rowsToSkip).first().find('td').length;

	var submission_string = '';
	var temp1 = '';

	$('#rosterTable tr').slice(rowsToSkip).each(function() {
		var player = {};
		num_players++;

		if (num_rows === 8) {
			player.female = true;
		}

		player.number = $(this).children('td:nth-child(1)').text().trim().replace(/\#/g, '');
		
		temp1 = $(this).children('td:nth-child(2)').text().trim().split('|');
		player.first_name = temp1.shift();  // get FIRST name(s)
		if (temp1[temp1.length-1].indexOf('(') >= 0) { // get (DRAFT) team
			player.draft_team = temp1.pop().substr(1,3);
		}
		player.last_name = temp1.join(" ").trim();
		
		player.year = $(this).children('td:nth-child(3)').text().slice(0, 2);
		player.year = player.year[0].toUpperCase() + player.year[1].toLowerCase();

		player.position = $(this).children('td:nth-child(4)').text().slice(0, 2).toLowerCase();
		
		if ($(this).children('td:nth-child(5)').text().trim()) {
			player.height = $(this).children('td:nth-child(5)').text().trim().split('-');
		}
		// set info index based on gender (W skips weight column)
		var i = 6;
		if (!player.female) { // parse weight (M)
			player.weight = $(this).children('td:nth-child('+i+')').text().trim();
			i = 7;
		}
		// parse handedness at some point?
		i++;
		// parse age at some point?
		i++;
		// parse hometown and prev team
		temp1 = $(this).children('td:nth-child('+i+')').text().trim().split(' / ');
		player.hometown = sanitizeHometown(temp1[0]).join(', ');
		player.prevteam = temp1[1].split(' |')[0].split(' (');
		if (player.prevteam[1]) {
			player.prevteam[1] = player.prevteam[1].split(')')[0];
			if (player.prevteam[1] === "USHS") { // clarify USHS by state
				player.prevteam[1] += "-" + getAbbr(player.hometown[1]);
			}
		} else {
			player.prevteam[1] = ' ';
		}
		if (player.prevteam[0].indexOf("N/A") >= 0) {
			player.prevteam[0] = ' ';
			player.prevteam[1] = ' ';
		}
		
		players[player.number] = player;

		parse_CHS_for_player(stats, player);

		submission_string += buildSubmissionLine(player);
	});

	$('#csv_textarea').val(submission_string.trim());
}

function buildSubmissionLine(p) {
	return [p.number, p.first_name, p.last_name, p.position, p.height, p.weight, p.year, p.hometown, p.stype, p.s1, p.s2, p.s3, p.s4, p.s5, p.s6, p.draft_team].join('|') + '\n';
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
					player.year = $(this).text().trim().slice(0, 2);
					player.year = player.year[0].toUpperCase() + player.year[1].toLowerCase()
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
					player.hometown = sanitizeHometown($(this).text().trim()).join(', ');
					break;
				case c.hometown_combined: // parse hometown from combined
					player.hometown = sanitizeHometown($(this).text().trim().split(' / ')[0]).join(', ');
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

	return [city, state];
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
