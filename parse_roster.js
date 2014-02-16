// Pass in the table HTML, and the number of initial rows not containing data
function parse_table_HTML(table_HTML, rowsToSkip) {
  if (rowsToSkip === undefined) {
    rowsToSkip = 1; // assume 1 header row
  }

  // Sanitize nbsp weirdness
  table_HTML = table_HTML.replace(/\&nbsp\;/g, ' ' );

  $('#rosterTable').html(table_HTML);
  $('#rosterTable table').css('font-size', '8pt');
  $('#tableEntry').hide();
  $('#showTableEntry').show();

  var num_players = 0;
  var num_rows = 0;
  var submission_string = '';
  var temp1 = '';
  var temp2 = '';
  var temp_stats = [[],[],[],[],[],[],[],[]];

  $('tr').slice(rowsToSkip).each(function(){
    num_players++;
    num_rows = $(this).find('td').length;

    $(this).find('td').each(function(index){
      switch (index){
      case 0: // parse number
        temp_stats[0] = $(this).text().trim().replace(/\#/g, '') + '|';
        break;
      case 1: // parse FIRST and LAST name and DRAFT
        temp1 = $(this).text().trim().replace("\'", "\\\'");
        temp2 = temp1.split(' ');
        
        if (temp2[temp2.length-1].indexOf('(') >= 0) {  // get (DRAFT) out
          temp2.pop();
        }

        temp_stats[1] = temp2.shift() + '|';  // get FIRST name
        
        var last_name = '';
        if (temp2[0]){
          temp2.forEach(function(elem){ // get all words in LAST name
            last_name += elem + ' ';
          });
          temp_stats[2] = last_name.trim() + '|';
        }
        break;
      case 2: // parse year
        temp1 = $(this).text().trim();
        temp2 = temp1.charAt(1).toLowerCase(); 
        temp_stats[6] = temp1.charAt(0) + temp2 + '|';
        break;
      case 3: // parse position
        temp_stats[3]= $(this).text().trim() + '|';
        break;
      case 4: // parse height
        temp_stats[4] = $(this).text().trim() + '|';
        break;
      case 5: // parse weight (M)
        if (num_rows == 9) {
            temp_stats[5] = $(this).text().trim() + '|';
          } else { //handedness (W)
            //temp_stats[5] = $(this).text().trim() + '|';
          }
        break;
      case 6: // parse handedness (M), age (W)
        
        break;
      case 7: // parse age (M), hometown + etc. (W)
        //submission_string += $(this).text().trim() + '|';
        if (num_rows == 8) {
          temp_stats[7] = $(this).text().trim().split(' / ')[0];
        }
        break;
      case 8: // parse hometown and prev team (M)
        temp1 = $(this).text().trim();
        temp2 = temp1.split(' / ');
        temp_stats[7] = temp2[0];
        break;
      }
    });
    
    temp_stats.forEach(function(elem){ // get all words in LAST name
      submission_string += elem;
    });
    submission_string += '\n';
    
  });

  $('#csv_textarea').val(submission_string.trim());

}

// Pass in an entire page of HTML
function parse_page_for_table(pageHTML) {
  
}

function validateFinalSubmission(){
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

