// Parse CHS stats table to get player stat 
function parseStatsCHS(t, stats_HTML) {
	$('#statsTable').html(stats_HTML);
	$('#statsTable table').css('font-size', '8pt');

	// skaters
	$('#statsTable .chssmallreg:eq(0)').find('tr').slice(2).each(function() {
		var tds = $(this).children();
		var n = tds.eq(0).text().trim();

		if (t[n]) {
			t[n].overall.s1 = tds.eq(4).text().trim();
			if (t[n].position === 'G') {
				t[n].stype = 'hg';
			} else {
				t[n].stype = 'hp';
				// overall stats
				t[n].overall.s2 = tds.eq(5).text().trim();
				t[n].overall.s3 = tds.eq(6).text().trim();
				t[n].overall.s4 = tds.eq(7).text().trim();
				t[n].overall.s5 = tds.eq(8).text().trim();
				if (t[n].overall.s5) { // convert PEN/MIN to PIM
					t[n].overall.s5 = t[n].overall.s5.split('/')[1];
				}
				t[n].overall.s6 = tds.eq(12).text().trim();
				if (t[n].overall.s6 === 'E') { // make 'E'ven into '0'
					t[n].overall.s6 = '0';
				} else if (!((t[n].overall.s6.indexOf('+')>-1)||(t[n].overall.s6.indexOf('-')>-1))) {
					t[n].overall.s6 = '';
					t[n].stype = 'ho'; // assign correct stype if no +/-
				}
				// conference stats
				t[n].conf.s1 = tds.eq(14).text().trim();
				t[n].conf.s2 = tds.eq(15).text().trim();
				t[n].conf.s3 = tds.eq(16).text().trim();
				t[n].conf.s4 = tds.eq(17).text().trim();
				t[n].conf.s5 = tds.eq(18).text().trim();
				if (t[n].conf.s5) { // convert PEN/MIN to PIM
					t[n].conf.s5 = t[n].conf.s5.split('/')[1];
				}
				t[n].conf.s6 = tds.eq(22).text().trim();
				if (t[n].conf.s6 === 'E') { // make 'E'ven into '0'
					t[n].conf.s6 = '0';
				} else if (t[n].stype === 'ho') {
					t[n].conf.s6 = '';
				}
				// career stats
				t[n].career.s1 = tds.eq(24).text().trim();
				t[n].career.s2 = tds.eq(25).text().trim();
				t[n].career.s3 = tds.eq(26).text().trim();
				t[n].career.s4 = tds.eq(27).text().trim();
			}
		} else {
			console.log('Player "'+n+'" not found.');
		}
	});

	// goaltenders
	var stat_group = 'overall';
	$('#statsTable .chssmallreg:eq(1)').find('tr').slice(1).each(function() {
		if ($(this).css('background-color') === 'rgb(51, 51, 51)') {
			if (stat_group === 'overall') {
				stat_group = 'conf';
				return true;
			} else if (stat_group === 'conf') {
				stat_group = 'career';
				return true;
			}
		}
		var tds = $(this).children();
		var n = tds.eq(0).text().trim();
		if (t[n]) {
			t[n][stat_group].s1 = tds.eq(3).text().trim();
			var record = tds.eq(10).text().trim().split('-');
			t[n][stat_group].s2 = record[0];
			t[n][stat_group].s3 = record[1];
			t[n][stat_group].s4 = record[2];
			t[n][stat_group].s5 = tds.eq(8).text().trim();
			t[n][stat_group].s6 = tds.eq(9).text().trim();
		} else {
			console.log('Player "'+n+'" not found.');
		}
	});

	// team stats

	//console.log(t);
}

// Pass in the table HTML, and the number of initial rows not containing data.
function parse_table_HTML(roster_HTML, stats_HTML, rowsToSkip) {
	if (rowsToSkip === undefined) {
		rowsToSkip = 1; // assume 1 header row
	}

	// Convert nbsp into something useful for splitting names
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
		var tds = $(this).children();
		var player = { overall:{}, conf:{}, career:{} };
		num_players++;

		if (num_rows === 8) {
			player.female = true;
		}
		
		// number
		player.number = tds.eq(0).text().trim().replace(/\#/g, '');
		// name
		temp1 = tds.eq(1).text().trim().replace("\'", "\\\'").split('|');
		player.first_name = temp1.shift();  // get FIRST name(s)
		if (temp1[temp1.length-1].indexOf('(') >= 0) { // get (DRAFT) team
			player.draft_team = temp1.pop().substr(1,3);
		}
		player.last_name = temp1.join(" ").trim();
		// year
		player.year = tds.eq(2).text().slice(0, 2);
		player.year = player.year[0].toUpperCase() + player.year[1].toLowerCase();
		//  position
		player.position = tds.eq(3).text().slice(0, 2);
		// height
		if (tds.eq(4).text().trim()) {
			player.height = tds.eq(4).text().trim();
		}
		// set info index based on gender (W skips weight)
		var i = 5;
		if (!player.female) { // weight (M)
			player.weight = tds.eq(i).text().trim();
			i = 6;
		}
		// parse handedness
		if (tds.eq(i).text().trim()) {
			player.hand = tds.eq(i).text().trim();
		}
		i++;
		// age
		if (tds.eq(i).text().replace('|', '').trim()) {
			player.age = tds.eq(i).text().replace('|', '').trim();
		}
		i++;
		// hometown and prev team
		temp1 = tds.eq(i).text().trim().split(' / ');
		player.hometown = sanitizeHometown(temp1[0]);
		player.prev_team = temp1[1].split(' |')[0].split(' (');
		if (player.prev_team[1]) {
			player.prev_team[1] = player.prev_team[1].split(')')[0];
			if (player.prev_team[1] === "USHS") { // clarify USHS by state
				player.prev_team[1] += "-" + getAbbr(player.hometown[1]);
			}
		} else {
			player.prev_team = player.prev_team[0];
		}
		if (player.prev_team[0].indexOf("N/A") >= 0) {
			player.prev_team = undefined;
		}
		if (tds.eq(i).children().length) {
			player.prev_college = tds.eq(i).children().first().text().slice(18, -1).split(' (');
		}

		player.hometown = player.hometown.join(', ');
	
		team[player.number] = player;
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
		var tds = $(this).children();
		var player = { overall:{}, conf:{}, career:{} };
		num_players++;

		if (c.num || (c.num == '0')) { // parse number
			player.number = tds.eq(c.num).text().trim().replace(/\#/g, '');
		}
		if (c.name) { // parse FIRST and LAST name
			temp1 = tds.eq(c.name).text().trim().replace('\'', '\\\'').split(' ');
			player.first_name = temp1.shift();  // get FIRST name (assume 1 first name)
			player.last_name = temp1.join(' ').trim();
		}
		if (c.yr) { // parse year
			player.year = tds.eq(c.yr).text().trim().slice(0, 2);
			player.year = player.year[0].toUpperCase() + player.year[1].toLowerCase();
		}
		if (c.pos) { // parse position
			player.position = tds.eq(c.pos).text().trim();
		}
		if (c.h) { // parse height
			player.height = tds.eq(c.h).text().trim();
		}
		if (c.w) { // parse weight
			player.weight = tds.eq(c.w).text().trim();
		}
		if (c.home) { // parse hometown isolated
			player.hometown = sanitizeHometown(tds.eq(c.home).text().trim()).join(', ');
		} else if (c.home_comb) { // parse hometown from combined
			player.hometown = sanitizeHometown(tds.eq(c.home_comb).text().trim().split(' / ')[0]).join(', ');
		}

		submission_string += buildSubmissionLine(player);
	});

	$('#csv_textarea').val(submission_string.trim());
}

// Find column order from SIDEARM HTML.
function discoverColumnsSIDEARM() {
	var cols = {};
	var index = 0;

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
