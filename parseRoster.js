// Parse CHS stats table to get player stat 
function parseStatsCHS(team, stats_HTML) {
	$('#statsTable').html(stats_HTML);
	$('#statsTable table').css('font-size', '8pt');
	t = team.players;

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
					t[n].overall.penalties = t[n].overall.s5.split('/')[0];
					t[n].overall.s5 = t[n].overall.s5.split('/')[1];
				}
				t[n].overall.s6 = tds.eq(12).text().trim();
				if (t[n].overall.s6 === 'E') { // make 'E'ven into '0'
					t[n].overall.s6 = '0';
				} else if (!((t[n].overall.s6.indexOf('+')>-1)||(t[n].overall.s6.indexOf('-')>-1))) {
					t[n].overall.s6 = '';
					t[n].stype = 'ho'; // assign correct stype if no +/-
				}
				// extra stats (for now)
				t[n].overall.ppg = tds.eq(9).text().trim(); // Power Play Goals
				t[n].overall.shg = tds.eq(10).text().trim(); // Short Handed Goals
				t[n].overall.gwg = tds.eq(11).text().trim(); // Game Winning Goals
				if (t[n].stype === 'hp') {
					t[n].overall.sog = tds.eq(13).text().trim(); // Shots on Goal
				} else {
					t[n].overall.gtg = tds.eq(12).text().trim(); // Game Tying Goals
					t[n].overall.eng = tds.eq(13).text().trim(); // Empty Net Goals
				}

				// conference stats
				t[n].conf.s1 = tds.eq(14).text().trim();
				t[n].conf.s2 = tds.eq(15).text().trim();
				t[n].conf.s3 = tds.eq(16).text().trim();
				t[n].conf.s4 = tds.eq(17).text().trim();
				t[n].conf.s5 = tds.eq(18).text().trim();
				if (t[n].conf.s5) { // convert PEN/MIN to PIM
					t[n].conf.penalties = t[n].conf.s5.split('/')[0];
					t[n].conf.s5 = t[n].conf.s5.split('/')[1];
				}
				t[n].conf.s6 = tds.eq(22).text().trim();
				if (t[n].conf.s6 === 'E') { // make 'E'ven into '0'
					t[n].conf.s6 = '0';
				} else if (t[n].stype === 'ho') {
					t[n].conf.s6 = '';
				}
				// extra stats (for now)
				t[n].conf.ppg = tds.eq(19).text().trim();
				t[n].conf.shg = tds.eq(20).text().trim();
				t[n].conf.gwg = tds.eq(21).text().trim();
				if (t[n].stype === 'hp') {
					t[n].conf.sog = tds.eq(23).text().trim();
				} else {	
					t[n].conf.gtg = tds.eq(22).text().trim();
					t[n].conf.eng = tds.eq(23).text().trim();
				}

				// career stats
				t[n].career.s1 = tds.eq(24).text().trim();
				t[n].career.s2 = tds.eq(25).text().trim();
				t[n].career.s3 = tds.eq(26).text().trim();
				t[n].career.s4 = tds.eq(27).text().trim();
			}
		} else {
			console.log('Skater "'+n+'" not found.');
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
			// extra stats (for now)
			t[n][stat_group].minutes = tds.eq(4).text().trim();
			t[n][stat_group].ga = tds.eq(5).text().trim();
			t[n][stat_group].saves = tds.eq(6).text().trim();
			t[n][stat_group].sog = tds.eq(7).text().trim();
			if (tds.eq(11).text().trim() != '---' ) {
				t[n][stat_group].win_pct = tds.eq(11).text().trim();
			}
			t[n][stat_group].gs = tds.eq(12).text().trim(); // Games Started
			t[n][stat_group].so = tds.eq(13).text().trim(); // Shut Outs
			if (tds.eq(14).text().trim()) {
				t[n][stat_group].pct_time = tds.eq(14).text().trim();
			}
		} else {
			console.log('Goaltender "'+n+'" not found.');
		}
	});

	// team stats
	var own = team.stats.own;
	var opp = team.stats.opp;
	var diff = team.stats.diff;

	var tds = $('#statsTable .chssmallreg:eq(2)').find('tr').slice(2).children();
	for (var i = 0; i < tds.length; i++) {
		tds[i].innerHTML = tds[i].innerHTML.replace(/ /g, '');
	}

	own.special_teams.overall.pp = tds.eq(1).text();
	own.special_teams.overall.pp_pct = tds.eq(2).text();
	own.special_teams.overall.pk = tds.eq(3).text();
	own.special_teams.overall.pk_pct = tds.eq(4).text();
	own.special_teams.overall.comb = tds.eq(5).text();
	own.special_teams.overall.comb_pct = tds.eq(6).text();
	own.special_teams.overall.ppc_g = tds.eq(7).text();
	own.special_teams.conf.pp = tds.eq(8).text();
	own.special_teams.conf.pp_pct = tds.eq(9).text();
	own.special_teams.conf.pk = tds.eq(10).text();
	own.special_teams.conf.pk_pct = tds.eq(11).text();
	own.special_teams.conf.comb = tds.eq(12).text();
	own.special_teams.conf.comb_pct = tds.eq(13).text();
	own.special_teams.conf.ppc_g = tds.eq(14).text();

	opp.special_teams.overall.pp = tds.eq(16).text();
	opp.special_teams.overall.pp_pct = tds.eq(17).text();
	opp.special_teams.overall.pk = tds.eq(18).text();
	opp.special_teams.overall.pk_pct = tds.eq(19).text();
	opp.special_teams.overall.comb = tds.eq(20).text();
	opp.special_teams.overall.comb_pct = tds.eq(21).text();
	opp.special_teams.overall.ppc_g = tds.eq(22).text();
	opp.special_teams.conf.pp = tds.eq(23).text();
	opp.special_teams.conf.pp_pct = tds.eq(24).text();
	opp.special_teams.conf.pk = tds.eq(25).text();
	opp.special_teams.conf.pk_pct = tds.eq(26).text();
	opp.special_teams.conf.comb = tds.eq(27).text();
	opp.special_teams.conf.comb_pct = tds.eq(28).text();
	opp.special_teams.conf.ppc_g = tds.eq(29).text();

	tds = $('#statsTable .chssmallreg:eq(3)').find('tr').slice(2).children();
	for (var i = 0; i < tds.length; i++) {
		tds[i].innerHTML = tds[i].innerHTML.replace(/ /g, '');
	}

	own.scoring.overall.first = tds.eq(1).text();
	own.scoring.overall.second = tds.eq(2).text();
	own.scoring.overall.third = tds.eq(3).text();
	own.scoring.overall.ot = tds.eq(4).text();
	own.scoring.overall.total = tds.eq(5).text();
	own.shots.overall.first = tds.eq(6).text();
	own.shots.overall.second = tds.eq(7).text();
	own.shots.overall.third = tds.eq(8).text();
	own.shots.overall.ot = tds.eq(9).text();
	own.shots.overall.sog = tds.eq(10).text();
	own.scoring.conf.first = tds.eq(11).text();
	own.scoring.conf.second = tds.eq(12).text();
	own.scoring.conf.third = tds.eq(13).text();
	own.scoring.conf.ot = tds.eq(14).text();
	own.scoring.conf.total = tds.eq(15).text();
	own.shots.conf.first = tds.eq(16).text();
	own.shots.conf.second = tds.eq(17).text();
	own.shots.conf.third = tds.eq(18).text();
	own.shots.conf.ot = tds.eq(19).text();
	own.shots.conf.sog = tds.eq(20).text();

	opp.scoring.overall.first = tds.eq(21).text();
	opp.scoring.overall.second = tds.eq(22).text();
	opp.scoring.overall.third = tds.eq(23).text();
	opp.scoring.overall.ot = tds.eq(24).text();
	opp.scoring.overall.total = tds.eq(25).text();
	opp.shots.overall.first = tds.eq(26).text();
	opp.shots.overall.second = tds.eq(27).text();
	opp.shots.overall.third = tds.eq(28).text();
	opp.shots.overall.ot = tds.eq(29).text();
	opp.shots.overall.sog = tds.eq(30).text();
	opp.scoring.conf.first = tds.eq(31).text();
	opp.scoring.conf.second = tds.eq(32).text();
	opp.scoring.conf.third = tds.eq(33).text();
	opp.scoring.conf.ot = tds.eq(34).text();
	opp.scoring.conf.total = tds.eq(35).text();
	opp.shots.conf.first = tds.eq(36).text();
	opp.shots.conf.second = tds.eq(37).text();
	opp.shots.conf.third = tds.eq(38).text();
	opp.shots.conf.ot = tds.eq(39).text();
	opp.shots.conf.sog = tds.eq(40).text();
	
	diff.scoring.overall.first = tds.eq(41).text();
	diff.scoring.overall.second = tds.eq(42).text();
	diff.scoring.overall.third = tds.eq(43).text();
	diff.scoring.overall.ot = tds.eq(44).text();
	diff.scoring.overall.total = tds.eq(45).text();
	diff.shots.overall.first = tds.eq(46).text();
	diff.shots.overall.second = tds.eq(47).text();
	diff.shots.overall.third = tds.eq(48).text();
	diff.shots.overall.ot = tds.eq(49).text();
	diff.shots.overall.sog = tds.eq(50).text();
	diff.scoring.conf.first = tds.eq(51).text();
	diff.scoring.conf.second = tds.eq(52).text();
	diff.scoring.conf.third = tds.eq(53).text();
	diff.scoring.conf.ot = tds.eq(54).text();
	diff.scoring.conf.total = tds.eq(55).text();
	diff.shots.conf.first = tds.eq(56).text();
	diff.shots.conf.second = tds.eq(57).text();
	diff.shots.conf.third = tds.eq(58).text();
	diff.shots.conf.ot = tds.eq(59).text();
	diff.shots.conf.sog = tds.eq(60).text();
	
	tds = $('#statsTable .chssmallreg:eq(4)').find('tr').slice(2).children();
	for (var i = 0; i < tds.length; i++) {
		tds[i].innerHTML = tds[i].innerHTML.replace(/ /g, '');
	}

	own.avgs.overall.goals = tds.eq(1).text();
	own.avgs.overall.assists = tds.eq(2).text();
	own.avgs.overall.points = tds.eq(3).text();
	own.avgs.overall.sog = tds.eq(4).text();
	own.avgs.overall.penalties = tds.eq(5).text();
	own.avgs.overall.pim = tds.eq(6).text();
	own.avgs.overall.ppg = tds.eq(7).text();
	own.avgs.conf.goals = tds.eq(8).text();
	own.avgs.conf.assists = tds.eq(9).text();
	own.avgs.conf.points = tds.eq(10).text();
	own.avgs.conf.sog = tds.eq(11).text();
	own.avgs.conf.penalties = tds.eq(12).text();
	own.avgs.conf.pim = tds.eq(13).text();
	own.avgs.conf.ppg = tds.eq(14).text();

	opp.avgs.overall.goals = tds.eq(16).text();
	opp.avgs.overall.assists = tds.eq(17).text();
	opp.avgs.overall.points = tds.eq(18).text();
	opp.avgs.overall.sog = tds.eq(19).text();
	opp.avgs.overall.penalties = tds.eq(20).text();
	opp.avgs.overall.pim = tds.eq(21).text();
	opp.avgs.overall.ppg = tds.eq(22).text();
	opp.avgs.conf.goals = tds.eq(23).text();
	opp.avgs.conf.assists = tds.eq(24).text();
	opp.avgs.conf.points = tds.eq(25).text();
	opp.avgs.conf.sog = tds.eq(26).text();
	opp.avgs.conf.penalties = tds.eq(27).text();
	opp.avgs.conf.pim = tds.eq(28).text();
	opp.avgs.conf.ppg = tds.eq(29).text();

	diff.avgs.overall.goals = tds.eq(31).text();
	diff.avgs.overall.assists = tds.eq(32).text();
	diff.avgs.overall.points = tds.eq(33).text();
	diff.avgs.overall.sog = tds.eq(34).text();
	diff.avgs.overall.penalties = tds.eq(35).text();
	diff.avgs.overall.pim = tds.eq(36).text();
	diff.avgs.overall.ppg = tds.eq(37).text();
	diff.avgs.conf.goals = tds.eq(38).text();
	diff.avgs.conf.assists = tds.eq(39).text();
	diff.avgs.conf.points = tds.eq(40).text();
	diff.avgs.conf.sog = tds.eq(41).text();
	diff.avgs.conf.penalties = tds.eq(42).text();
	diff.avgs.conf.pim = tds.eq(43).text();
	diff.avgs.conf.ppg = tds.eq(44).text();

	tds = $('#statsTable .chssmallreg:eq(5)').find('tr').slice(1).children();
	for (var i = 0; i < tds.length; i++) {
		tds[i].innerHTML = tds[i].innerHTML.replace(/ /g, '');
	}

	own.situash.overall.home_total = tds.eq(2).text();
	own.situash.overall.home_record = tds.eq(3).text().split('-');
	own.situash.overall.away_total = tds.eq(5).text();
	own.situash.overall.away_record = tds.eq(6).text().split('-');
	own.situash.overall.neutral_total = tds.eq(8).text();
	own.situash.overall.neutral_record = tds.eq(9).text().split('-');
	own.situash.conf.home_total = tds.eq(12).text();
	own.situash.conf.home_record = tds.eq(13).text().split('-');
	own.situash.conf.away_total = tds.eq(15).text();
	own.situash.conf.away_record = tds.eq(16).text().split('-');
	own.situash.conf.neutral_total = tds.eq(18).text();
	own.situash.conf.neutral_record = tds.eq(19).text().split('-');

	own.situash.overall.ahead_first_total = tds.eq(22).text();
	own.situash.overall.ahead_first_record = tds.eq(23).text().split('-');
	own.situash.overall.behind_first_total = tds.eq(25).text();
	own.situash.overall.behind_first_record = tds.eq(26).text().split('-');
	own.situash.overall.even_first_total = tds.eq(28).text();
	own.situash.overall.even_first_record = tds.eq(29).text().split('-');
	own.situash.conf.ahead_first_total = tds.eq(32).text();
	own.situash.conf.ahead_first_record = tds.eq(33).text().split('-');
	own.situash.conf.behind_first_total = tds.eq(35).text();
	own.situash.conf.behind_first_record = tds.eq(36).text().split('-');
	own.situash.conf.even_first_total = tds.eq(38).text();
	own.situash.conf.even_first_record = tds.eq(39).text().split('-');

	own.situash.overall.ahead_second_total = tds.eq(42).text();
	own.situash.overall.ahead_second_record = tds.eq(43).text().split('-');
	own.situash.overall.behind_second_total = tds.eq(45).text();
	own.situash.overall.behind_second_record = tds.eq(46).text().split('-');
	own.situash.overall.even_second_total = tds.eq(48).text();
	own.situash.overall.even_second_record = tds.eq(49).text().split('-');
	own.situash.conf.ahead_second_total = tds.eq(52).text();
	own.situash.conf.ahead_second_record = tds.eq(53).text().split('-');
	own.situash.conf.behind_second_total = tds.eq(55).text();
	own.situash.conf.behind_second_record = tds.eq(56).text().split('-');
	own.situash.conf.even_second_total = tds.eq(58).text();
	own.situash.conf.even_second_record = tds.eq(59).text().split('-');
	
	own.situash.overall.margin_one_total = tds.eq(62).text();
	own.situash.overall.margin_one_record = tds.eq(63).text().split('-');
	own.situash.overall.margin_two_total = tds.eq(65).text();
	own.situash.overall.margin_two_record = tds.eq(66).text().split('-');
	own.situash.overall.margin_threeup_total = tds.eq(68).text();
	own.situash.overall.margin_threeup_record = tds.eq(69).text().split('-');
	own.situash.conf.margin_one_total = tds.eq(72).text();
	own.situash.conf.margin_one_record = tds.eq(73).text().split('-');
	own.situash.conf.margin_two_total = tds.eq(75).text();
	own.situash.conf.margin_two_record = tds.eq(76).text().split('-');
	own.situash.conf.margin_threeup_total = tds.eq(78).text();
	own.situash.conf.margin_threeup_record = tds.eq(79).text().split('-');

	own.situash.overall.first_goal_for_total = tds.eq(82).text();
	own.situash.overall.first_goal_for_record = tds.eq(83).text().split('-');
	own.situash.overall.first_goal_vs_total = tds.eq(85).text();
	own.situash.overall.first_goal_vs_record = tds.eq(86).text().split('-');
	own.situash.conf.first_goal_for_total = tds.eq(90).text();
	own.situash.conf.first_goal_for_record = tds.eq(91).text().split('-');
	own.situash.conf.first_goal_vs_total = tds.eq(93).text();
	own.situash.conf.first_goal_vs_record = tds.eq(94).text().split('-');

	console.log(team.stats);
}

// Pass in the table HTML, and the number of initial rows not containing data.
function parse_table_HTML(roster_HTML, stats_HTML, skip_rows) {
	if (skip_rows === undefined) {
		skip_rows = 1; // assume 1 header row
	}

	// Convert nbsp into something useful for splitting names
	roster_HTML = roster_HTML.replace(/\&nbsp\;/g, '|' );

	$('#rosterTable').html(roster_HTML);
	$('#rosterTable table').css('font-size', '8pt');
	$('#rosterTable').show();
	$('#tableEntry').hide();

	var team = {
		players: [],
		stats: {
			own: {
				special_teams: { overall:{}, conf:{} },
				scoring: { overall:{}, conf:{} },
				shots: { overall:{}, conf:{} },
				avgs: { overall:{}, conf:{} },
				situash: { overall:{}, conf:{} },
			},
			opp: {
				special_teams: { overall:{}, conf:{} },
				scoring: { overall:{}, conf:{} },
				shots: { overall:{}, conf:{} },
				avgs: { overall:{}, conf:{} },	
			},
			diff: {
				scoring: { overall:{}, conf:{} },
				shots: { overall:{}, conf:{} },
				avgs: { overall:{}, conf:{} },
			},
		},
	};
	var num_players = 0;
	var num_rows = $('#rosterTable tr').slice(skip_rows).first().find('td').length;

	var submission_string = '';
	var temp = '';

	$('#rosterTable tr').slice(skip_rows).each(function() {
		var tds = $(this).children();
		var player = { overall:{}, conf:{}, career:{} };
		num_players++;

		if (num_rows === 8) {
			player.female = true;
		}
		
		// number
		player.number = tds.eq(0).text().trim().replace(/\#/g, '');
		// name
		temp = tds.eq(1).text().trim().replace("\'", "\\\'").split('|');
		player.first_name = temp.shift();  // get FIRST name(s)
		if (temp[temp.length-1].indexOf('(') >= 0) { // get (DRAFT) team
			player.draft_team = temp.pop().substr(1,3);
		}
		player.last_name = temp.join(" ").trim();
		// year
		player.year = tds.eq(2).text().slice(0, 2);
		player.year = player.year[0].toUpperCase() + player.year[1].toLowerCase();
		// position
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
		temp = tds.eq(i).text().trim().split(' / ');
		player.hometown = sanitizeHometown(temp[0]);
		player.prev_team = temp[1].split(' |')[0].split(' (');
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
	
		team.players[player.number] = player;
	});

	parseStatsCHS(team, stats_HTML);

	team.players.forEach( function (p) {
		submission_string += buildSubmissionLine(p);
	});

	$('#csv_textarea').val(submission_string.trim());
}

// Pass in SIDEARM HTML, and the number of initial rows not containing data.
function parseRosterSIDEARM(table_HTML, skip_rows) {
	if (skip_rows === undefined) {
		skip_rows = 1; // assume 1 header row
	}

	// Sanitize nbsp weirdness
	table_HTML = table_HTML.replace(/\&nbsp\;/g, '' );

	$('#rosterSIDEARM').html(table_HTML);
	$('#rosterSIDEARM table').css('font-size', '8pt');
	$('#boxSIDEARM').hide();

	c = discoverColumnsSIDEARM();

	var num_players = 0;
	var submission_string = '';
	var temp = '';

	$('#rosterSIDEARM tr').slice(skip_rows).each(function() {
		var tds = $(this).children();
		var player = { overall:{}, conf:{}, career:{} };
		num_players++;

		if (c.num || (c.num == '0')) { // number
			player.number = tds.eq(c.num).text().trim().replace(/\#/g, '');
		}
		if (c.name) { // FIRST and LAST name
			temp = tds.eq(c.name).text().trim().replace('\'', '\\\'').split(' ');
			player.first_name = temp.shift();  // get FIRST name (assume 1 first name)
			player.last_name = temp.join(' ').trim();
		}
		if (c.yr) { // year
			player.year = tds.eq(c.yr).text().trim().slice(0, 2);
			player.year = player.year[0].toUpperCase() + player.year[1].toLowerCase();
		}
		if (c.pos) { // position
			player.position = tds.eq(c.pos).text().trim();
		}
		if (c.h) { // height
			player.height = tds.eq(c.h).text().trim();
		}
		if (c.w) { // weight
			player.weight = tds.eq(c.w).text().trim();
		}
		if (c.home) { // hometown isolated
			player.hometown = sanitizeHometown(tds.eq(c.home).text().trim()).join(', ');
		} else if (c.home_comb) { // hometown combined
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
