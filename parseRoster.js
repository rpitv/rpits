// Parse CHS stats table to get player stat 
function parseStatsCHS(t, stats_HTML) {
	$('#statsTable').html(stats_HTML);
	$('#statsTable table').css('font-size', '8pt');

	// skaters
	$('#statsTable .chssmallreg:eq(0)').find('tr').slice(2).each(function() {
		var n = $(this).children('td').first().text().trim();

		if (t[n]) {
			t[n].overall.s1 = $(this).children('td:nth-child(5)').text().trim();
			if (t[n].position === 'G') {
				t[n].stype = 'hg';
			} else {
				t[n].stype = 'hp';
				t[n].overall.s2 = $(this).children('td:nth-child(6)').text().trim();
				t[n].overall.s3 = $(this).children('td:nth-child(7)').text().trim();
				t[n].overall.s4 = $(this).children('td:nth-child(8)').text().trim();
				t[n].overall.s5 = $(this).children('td:nth-child(9)').text().trim();
				if (t[n].overall.s5) { // convert PEN/MIN to PIM
					t[n].overall.s5 = t[n].overall.s5.split('/')[1];
				}
				t[n].overall.s6 = $(this).children('td:nth-child(13)').text().trim();
				if (t[n].overall.s6 === 'E') { // make 'E'ven into '0'
					t[n].overall.s6 = '0';
				} else if (!((t[n].overall.s6.indexOf('+')>-1)||(t[n].overall.s6.indexOf('-')>-1))) {
					t[n].overall.s6 = '';
					t[n].stype = 'ho'; // assign correct stype if no +/-
				}

			}
		} else {
			console.log('Player "'+n+'" not found.');
		}
	});

	// goaltenders
	$('#statsTable .chssmallreg:eq(1)').find('tr').slice(1).each(function() {
		if ($(this).css('background-color')!='rgba(0, 0, 0, 0)') {
			return false; // only care about overall stats right now
		}		

		var n = $(this).children('td').first().text().trim();
		if (t[n]) {
			var record = $(this).children('td:nth-child(11)').text().trim().split('-');
			t[n].overall.s2 = record[0];
			t[n].overall.s3 = record[1];
			t[n].overall.s4 = record[2];
			t[n].overall.s5 = $(this).children('td:nth-child(9)').text().trim();
			t[n].overall.s6 = $(this).children('td:nth-child(10)').text().trim();
		} else {
			console.log('Player "'+n+'" not found.');
		}
	});


}

// Pass in the table HTML, and the number of initial rows not containing data.
function parse_table_HTML(roster_HTML, stats_HTML, rowsToSkip) {
	if (rowsToSkip === undefined) {
		rowsToSkip = 1; // assume 1 header row
	}

	// Sanitize nbsp weirdness - or NOT because it is useful for names
	roster_HTML = roster_HTML.replace(/\&nbsp\;/g, '|' );

	$('#rosterTable').html(roster_HTML);
	$('#rosterTable table').css('font-size', '8pt');
	$('#rosterTable').show();
	$('#tableEntry').hide();

	var team = [];
	var num_players = 0;
	var num_rows = $('#rosterTable tr').slice(rowsToSkip).first().find('td').length;

	var submission_string = '';
	var temp1 = '';

	$('#rosterTable tr').slice(rowsToSkip).each(function() {
		var player = { overall:{}, conf:{}, career:{} };
		num_players++;

		if (num_rows === 8) {
			player.female = true;
		}
		
		// parse number
		player.number = $(this).children('td:nth-child(1)').text().trim().replace(/\#/g, '');
		// parse name
		temp1 = $(this).children('td:nth-child(2)').text().trim().replace("\'", "\\\'").split('|');
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
		
		team[player.number] = player;

		//parse_CHS_text_for_player(stats_HTML, player);
	});

	parseStatsCHS(team, stats_HTML);

	team.forEach( function (p) {
		submission_string += buildSubmissionLine(p);
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
		var player = { overall:{}, conf:{}, career:{} };

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
		p.overall.s1,
		p.overall.s2,
		p.overall.s3,
		p.overall.s4,
		p.overall.s5,
		p.overall.s6,
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
