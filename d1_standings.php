<?php

function add_text() {
	$width = 1045;
	$height = 680;
	$team = new Imagick();
	$team->newPseudoImage($width, $height, 'xc:none');
	
	$table_data = table_parse();
	
	$organization = "";
	
	$places = "";
	$teams = "";
	$pts = "";
	$record_c = "";
	$record_o = "";
	
	$previous_pts = $table_data[0][1] + 1;
	$place_count = 1;
	
	for($x = 0 ; $x < count($table_data); $x++)
	{
		//Adds Text 
		if($x == count($table_data)-1)
		{
			$teams += $table_data[$x][0];
			$pts += $table_data[$x][1];
			$record_c += $table_data[$x][2];
			$record_o += $table_data[$x][3];
		}
		else
		{
			$teams += $table_data[$x][0];
			$pts += $table_data[$x][1];
			$record_c += $table_data[$x][2];
			$record_o += $table_data[$x][3];
			$teams += "\n";
			$pts += "\n";
			$record_c += "\n";
			$record_o += "\n";
		}
		//Adds Teams Place
		if($table_data[$x][1] < $previous_pts)
		{
			$pts += $place_count;
			$pts += "\n";
			$place_count++;
		}
		else
		{			
			$pts += "\n";
		}
		//Corrects for different name
		if($table_data[$x][0] == "St. Lawrence")
		{
			$organization = fetchOrg("slu");
		}
		else
		{
			$organization = fetchOrg(strtolower($table_data[$x][0]));
		}
		placeImage($team,array('w'=>46,'h'=>46,'x'=>520,'y'=>428 +50*$x ,'path'=>$teams[$table_data[2][0]]['logo']));
	}
	
	plainText($team,array('text'=>$places,'w'=>575,'h'=>680,'x'=>445,'y'=>375,'gravity'=>'west','font'=>'fontN','color'=>'white'));
	plainText($team,array('text'=>$teams,'w'=>575,'h'=>680,'x'=>595,'y'=>375,'gravity'=>'west','font'=>'fontN','color'=>'white'));
	plainText($team,array('text'=>$pts,'w'=>575,'h'=>680,'x'=>930,'y'=>375,'gravity'=>'west','font'=>'fontN','color'=>'white'));
	plainText($team,array('text'=>$record_c,'w'=>575,'h'=>680,'x'=>1060,'y'=>375,'gravity'=>'west','font'=>'fontN','color'=>'white'));
	plainText($team,array('text'=>$record_o,'w'=>575,'h'=>680,'x'=>1290,'y'=>375,'gravity'=>'west','font'=>'fontN','color'=>'white'));
	return $team;
}

function table_parse()
{
	$wrpi_stats = fopen($o['liveStatsXML'], "r");
	$wrpi_stats = stream_get_contents($wrpi_stats);
	$wrpi_stats = mb_convert_encoding($wrpi_stats, 'UTF-8', 'ASCII');
	$wrpi_stats = addslashes($wrpi_stats);
	$wrpi_stats = str_replace([chr(10), chr(13)], '', $wrpi_stats);  // fix newline issues
	$wrpi_stats = stristr($wrpi_stats, "<tr align=\\\"center\\\" bgcolor=\\\"#404040\\\"><th align=\\\"left\\\"><font color=\\\"#ffffff\\\">"); // get only stat table data
	$wrpi_stats = substr($wrpi_stats, 0, (strripos($wrpi_stats, "</tr></table></center><table width=\\\"1000\\\">"))); 
	$wrpi_array = explode("</tr>",$wrpi_stats);
	
	$lines = array();
	
	for($x = 1; $x <=13 ; $x++  )
	{	
		$wrpi_array_test = explode("<td",$wrpi_stats[$x] );	
		$team_string = stristr($wrpi_array_test[1], ">"); // get only stat table data
		$team_string = substr($team_string, 0, (strripos($team_string, "<"))); 
		$team_string = ltrim($team_string, '>');
		$pts_string = stristr($wrpi_array_test[4], ">"); // get only stat table data
		$pts_string = substr($pts_string, 0, (strripos($pts_string, "<"))); 
		$pts_string = ltrim($pts_string, '>');
		$conf_record_string = stristr($wrpi_array_test[3], ">"); // get only stat table data
		$conf_record_string = substr($conf_record_string, 0, (strripos($conf_record_string, "<"))); 
		$conf_record_string = ltrim($conf_record_string, '>');
		$overall_record_string = stristr($wrpi_array_test[9], ">"); // get only stat table data
		$overall_record_string = substr($overall_record_string, 0, (strripos($overall_record_string, "<"))); 
		$overall_record_string = ltrim($overall_record_string, '>');
		
		$lines[x-1] = array($team_string,$pts_string,$conf_record_string,$overall_record_string);
	}
	return $lines;
}

function gameSummary(&$canvas,$o) { 
	
	$width = 1060;
	$height = 680;
	$teams = new Imagick();
	$teams->newPseudoImage($width, $height, 'xc:none');
	
	$path = $o['wrpiHTML'];
	
	$teams->compositeImage(add_text(),imagick::COMPOSITE_OVER,0,0);	

	addGeoToCanvas($canvas,array(
			'type'=>'flexBox',
			'w'=>1920,
			'h'=>1080,
			'x'=>0,
			'y'=>0,

			'bodyText'=>'',
			'boxHeight'=>680,
			'boxWidth'=>1060,
			'boxOffset'=>'auto',
			'boxPadding'=>10,

			'titleColor'=>$o['barColor'],
			'titleHeight'=>'70',
			'titleText'=>$o['titleText'],
			'titleGravity'=>'center',
			
			
	));

	$canvas->compositeImage($teams,imagick::COMPOSITE_OVER,(1920-980)/2,1080-50-10);

}

?>
