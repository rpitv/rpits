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

// Pass in the table HTML, and the number of initial rows not containing data.
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
		
		// parse number
		player.number = $(this).children('td:nth-child(1)').text().trim().replace(/\#/g, '');
		// parse name
		temp1 = $(this).children('td:nth-child(2)').text().trim().split('|');
		player.first_name = temp1.shift();  // get FIRST name(s)
		if (temp1[temp1.length-1].indexOf('(') >= 0) { // get (DRAFT) team
			player.draft_team = temp1.pop().substr(1,3);
		}
		player.last_name = temp1.join(" ").trim();
		// parse year
		player.year = $(this).children('td:nth-child(3)').text().slice(0, 2);
		player.year = player.year[0].toUpperCase() + player.year[1].toLowerCase();
		// parse position
		player.position = $(this).children('td:nth-child(4)').text().slice(0, 2);
		// parse height
		if ($(this).children('td:nth-child(5)').text().trim()) {
			player.height = $(this).children('td:nth-child(5)').text().trim();
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

// Pass in SIDEARM HTML, and the number of initial rows not containing data.
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
	var submission_string = '';
	var temp1 = '';

	$('#rosterSIDEARM tr').slice(rowsToSkip).each(function() {
		var player = {};
		num_players++;

		if (c.num) { // parse number
			player.number = $(this).children('td:nth-child('+c.num+')').text().trim().replace(/\#/g, '');
		}
		if (c.name) { // parse FIRST and LAST name
			temp1 = $(this).children('td:nth-child('+c.name+')').text().trim().replace('\'', '\\\'').split(' ');
			player.first_name = temp1.shift();  // get FIRST name (assume 1 first name)
			player.last_name = temp1.join(' ').trim();
		}
		if (c.yr) { // parse year
			player.year = $(this).children('td:nth-child('+c.yr+')').text().trim().slice(0, 2);
			player.year = player.year[0].toUpperCase() + player.year[1].toLowerCase();
		}
		if (c.pos) { // parse position
			player.position = $(this).children('td:nth-child('+c.pos+')').text().trim();
		}
		if (c.h) { // parse height
			player.height = $(this).children('td:nth-child('+c.h+')').text().trim();
		}
		if (c.w) { // parse weight
			player.weight = $(this).children('td:nth-child('+c.w+')').text().trim();
		}
		if (c.home) { // parse hometown isolated
			player.hometown = sanitizeHometown($(this).children('td:nth-child('+c.home+')').text().trim()).join(', ');
		} else if (c.home_comb) { // parse hometown from combined
			player.hometown = sanitizeHometown($(this).children('td:nth-child('+c.home_comb+')').text().trim().split(' / ')[0]).join(', ');
		}

		submission_string += buildSubmissionLine(player);
	});

	$('#csv_textarea').val(submission_string.trim());
}

// Find column order from SIDEARM HTML.
function discoverColumnsSIDEARM() {
	var cols = {};
	var index = 1; // start at 1 because nth-child does for some reason

	$("#other_page .default_dgrd_header th").each(function() {
		if ($(this).hasClass('roster_dgrd_header_no')) {
			cols.num = index;
		} else if ($(this).hasClass('roster_dgrd_header_full_name')) {
			cols.name = index;
		} else if ($(this).hasClass('roster_dgrd_header_rp_position_short')) {
			cols.pos = index;
		} else if ($(this).hasClass('roster_dgrd_header_rp_position_long')) {
			cols.pos = index;
		} else if ($(this).hasClass('roster_dgrd_header_height')) {
			cols.h = index;
		} else if ($(this).hasClass('roster_dgrd_header_rp_weight')) {
			cols.w = index;
		} else if ($(this).hasClass('roster_dgrd_header_academic_year')) {
			cols.yr = index;
		} else if ($(this).hasClass('roster_dgrd_header_player_hometown')) {
			cols.home = index;
		} else if ($(this).hasClass('roster_dgrd_player_hometown')) {
			cols.home = index;
		} else if ($(this).hasClass('roster_dgrd_header_highschool')) {
			cols.hs = index;
		} else if ($(this).hasClass('roster_dgrd_header_hometownhighschool')) {
			cols.home_comb = index;
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

	if (place.indexOf(",")<0) { return [place]; }

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

// Build a formatted line of data for a given player.
function buildSubmissionLine(p) {
	return [
		p.number,
		p.first_name,
		p.last_name,
		p.position,
		p.height,
		p.weight,
		p.year,
		p.hometown,
		p.stype,
		p.s1,
		p.s2,
		p.s3,
		p.s4,
		p.s5,
		p.s6,
		p.draft_team,
	].join('|') + '\n';
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
