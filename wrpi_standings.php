<?php

function loadWRPISeasonStandings_wh($team_stat,$eventId) {
	

	$fn = fopen("wrpi_wh_stats.txt","r");
	$result = fgets($fn);
	//$lines = array();
	$result = str_replace("\n", '', $result);
	if($result ==  date("l"))
	{
		$line = null;
		while(($line = fgets($fn)) !== false)
		{
			$line = explode(",",$line);
			$lines[] = $line;
		}
	}
	else
	{	
		$wrpi_stats = fopen("http://wrpi.org/sports/wh_ecac.html", "r");
		$wrpi_stats = stream_get_contents($wrpi_stats);
		$wrpi_stats = mb_convert_encoding($wrpi_stats, 'UTF-8', 'ASCII');
		$wrpi_stats = addslashes($wrpi_stats);
		$wrpi_stats = str_replace([chr(10), chr(13)], '', $wrpi_stats);  // fix newline issues
		$wrpi_stats = stristr($wrpi_stats, "<tr align=\\\"center\\\" bgcolor=\\\"#404040\\\"><th align=\\\"left\\\"><font color=\\\"#ffffff\\\">"); // get only stat table data
		$wrpi_stats = substr($wrpi_stats, 0, (strripos($wrpi_stats, "</tr></table></center><table width=\\\"1000\\\">"))); 
		$wrpi_array = explode("</tr>",$wrpi_stats);
		
		
		
		
		$whfile = fopen( '/var/www/rpits/wrpi_wh_stats.txt', "w+");
		$today = date("l");
		fwrite($whfile,  date("l"));
		fwrite($whfile,  "\n");
		
		
		
		for($x = 1; $x <13 ; $x++  )
		{	
			$wrpi_array_test = explode("<td",$wrpi_array[$x] );	
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
			
			fwrite($whfile, $team_string);
			fwrite($whfile, ",");
			fwrite($whfile, $pts_string);
			fwrite($whfile, ",");
			fwrite($whfile, $conf_record_string);
			fwrite($whfile, ",");
			fwrite($whfile, $overall_record_string);
			fwrite($whfile, ",\n");
			$lines[] = array($team_string, $pts_string, $conf_record_string, $overall_record_string);
		}
		
	}
	
	$text = array();
	$text[0] = "Team\n";
	$text[1] = "Pts \n"; 
	$text[2] = "Conf \n"; 
	$text[3] = "Overall \n";
	for($x = 0; $x < 12; $x++)
	{
		if($x == 11)
		{
			$text[0] .= $lines[$x][0];
			$text[1] .= $lines[$x][1];
			$text[2] .= $lines[$x][2];
			$text[3] .= $lines[$x][3];
		}
		else
		{
			$text[0] .= $lines[$x][0];
			$text[1] .= $lines[$x][1];
			$text[2] .= $lines[$x][2];
			$text[3] .= $lines[$x][3];
			
			$text[0] .= "\n";
			$text[1] .= "\n";
			$text[2] .= "\n";
			$text[3] .= "\n";
		}
	}
	
	if($team_stat == "team")
	{
		return($text[0]);
	}
	elseif($team_stat == "pts")
	{
		return($text[1]);
	}
	elseif($team_stat == "conf")
	{
		return($text[2]);
	}
	elseif($team_stat == "overall")
	{
		return($text[3]);
	}
	elseif($team_stat == "1")
	{
		if($lines[0][0] == "St. Lawrence")
		{
			return(getToken("o.slu.logo",$eventId));
		}
		elseif($lines[0][0] == "Rensselaer")
		{
			return(getToken("o.rpi.logo",$eventId));
		}
		elseif($lines[0][0] == "Clarkson")
		{
			return(getToken("o.clark.logo",$eventId));
		}
		else
		{			
			return(getToken("o." . strtolower($lines[0][0]) . ".logo",$eventId));
		}
	}
	elseif($team_stat == "2")
	{
		if($lines[1][0] == "St. Lawrence")
		{
			return(getToken("o.slu.logo",$eventId));
		}
		elseif($lines[1][0] == "Rensselaer")
		{
			return(getToken("o.rpi.logo",$eventId));
		}
		elseif($lines[1][0] == "Clarkson")
		{
			return(getToken("o.clark.logo",$eventId));
		}
		else
		{			
			return(getToken("o." . strtolower($lines[1][0]) . ".logo",$eventId));
		}
	}
	elseif($team_stat == "3")
	{
		if($lines[2][0] == "St. Lawrence")
		{
			return(getToken("o.slu.logo",$eventId));
		}
		elseif($lines[2][0] == "Rensselaer")
		{
			return(getToken("o.rpi.logo",$eventId));
		}
		elseif($lines[2][0] == "Clarkson")
		{
			return(getToken("o.clark.logo",$eventId));
		}
		else
		{			
			return(getToken("o." . strtolower($lines[2][0]) . ".logo",$eventId));
		}
	}
	elseif($team_stat == "4")
	{
		if($lines[3][0] == "St. Lawrence")
		{
			return(getToken("o.slu.logo",$eventId));
		}
		elseif($lines[3][0] == "Rensselaer")
		{
			return(getToken("o.rpi.logo",$eventId));
		}
		elseif($lines[3][0] == "Clarkson")
		{
			return(getToken("o.clark.logo",$eventId));
		}
		else
		{			
			return(getToken("o." . strtolower($lines[3][0]) . ".logo",$eventId));
		}
	}
	elseif($team_stat == "5")
	{
		if($lines[4][0] == "St. Lawrence")
		{
			return(getToken("o.slu.logo",$eventId));
		}
		elseif($lines[4][0] == "Rensselaer")
		{
			return(getToken("o.rpi.logo",$eventId));
		}
		elseif($lines[4][0] == "Clarkson")
		{
			return(getToken("o.clark.logo",$eventId));
		}
		else
		{			
			return(getToken("o." . strtolower($lines[4][0]) . ".logo",$eventId));
		}
	}
	elseif($team_stat == "6")
	{
		if($lines[5][0] == "St. Lawrence")
		{
			return(getToken("o.slu.logo",$eventId));
		}
		elseif($lines[5][0] == "Rensselaer")
		{
			return(getToken("o.rpi.logo",$eventId));
		}
		elseif($lines[5][0] == "Clarkson")
		{
			return(getToken("o.clark.logo",$eventId));
		}
		else
		{			
			return(getToken("o." . strtolower($lines[5][0]) . ".logo",$eventId));
		}
	}
	elseif($team_stat == "7")
	{
		if($lines[6][0] == "St. Lawrence")
		{
			return(getToken("o.slu.logo",$eventId));
		}
		elseif($lines[6][0] == "Rensselaer")
		{
			return(getToken("o.rpi.logo",$eventId));
		}
		elseif($lines[6][0] == "Clarkson")
		{
			return(getToken("o.clark.logo",$eventId));
		}
		else
		{			
			return(getToken("o." . strtolower($lines[6][0]) . ".logo",$eventId));
		}
	}
	elseif($team_stat == "8")
	{
		if($lines[7][0] == "St. Lawrence")
		{
			return(getToken("o.slu.logo",$eventId));
		}
		elseif($lines[7][0] == "Rensselaer")
		{
			return(getToken("o.rpi.logo",$eventId));
		}
		elseif($lines[7][0] == "Clarkson")
		{
			return(getToken("o.clark.logo",$eventId));
		}
		else
		{			
			return(getToken("o." . strtolower($lines[7][0]) . ".logo",$eventId));
		}
	}
	elseif($team_stat == "9")
	{
		if($lines[8][0] == "St. Lawrence")
		{
			return(getToken("o.slu.logo",$eventId));
		}
		elseif($lines[8][0] == "Rensselaer")
		{
			return(getToken("o.rpi.logo",$eventId));
		}
		elseif($lines[8][0] == "Clarkson")
		{
			return(getToken("o.clark.logo",$eventId));
		}
		else
		{			
			return(getToken("o." . strtolower($lines[8][0]) . ".logo",$eventId));
		}
	}
	elseif($team_stat == "10")
	{
		if($lines[9][0] == "St. Lawrence")
		{
			return(getToken("o.slu.logo",$eventId));
		}
		elseif($lines[9][0] == "Rensselaer")
		{
			return(getToken("o.rpi.logo",$eventId));
		}
		elseif($lines[9][0] == "Clarkson")
		{
			return(getToken("o.clark.logo",$eventId));
		}
		else
		{			
			return(getToken("o." . strtolower($lines[9][0]) . ".logo",$eventId));
		}
	}
	elseif($team_stat == "11")
	{
		if($lines[10][0] == "St. Lawrence")
		{
			return(getToken("o.slu.logo",$eventId));
		}
		elseif($lines[10][0] == "Rensselaer")
		{
			return(getToken("o.rpi.logo",$eventId));
		}
		elseif($lines[10][0] == "Clarkson")
		{
			return(getToken("o.clark.logo",$eventId));
		}
		else
		{			
			return(getToken("o." . strtolower($lines[10][0]) . ".logo",$eventId));
		}
	}
	elseif($team_stat == "12")
	{
		if($lines[11][0] == "St. Lawrence")
		{
			return(getToken("o.slu.logo",$eventId));
		}
		elseif($lines[11][0] == "Rensselaer")
		{
			return(getToken("o.rpi.logo",$eventId));
		}
		elseif($lines[11][0] == "Clarkson")
		{
			return(getToken("o.clark.logo",$eventId));
		}
		else
		{			
			return(getToken("o." . strtolower($lines[11][0]) . ".logo",$eventId));
		}
	}
}

function loadWRPISeasonStandings_mh($team_stat,$eventId) {
	

	$fn = fopen("wrpi_mh_stats.txt","r");
	$result = fgets($fn);
	//$lines = array();
	$result = str_replace("\n", '', $result);
	if($result ==  date("l"))
	{
		$line = null;
		while(($line = fgets($fn)) !== false)
		{
			$line = explode(",",$line);
			$lines[] = $line;
		}
	}
	else
	{	
		$wrpi_stats = fopen("http://wrpi.org/sports/mh_ecac.html", "r");
		$wrpi_stats = stream_get_contents($wrpi_stats);
		$wrpi_stats = mb_convert_encoding($wrpi_stats, 'UTF-8', 'ASCII');
		$wrpi_stats = addslashes($wrpi_stats);
		$wrpi_stats = str_replace([chr(10), chr(13)], '', $wrpi_stats);  // fix newline issues
		$wrpi_stats = stristr($wrpi_stats, "<tr align=\\\"center\\\" bgcolor=\\\"#404040\\\"><th align=\\\"left\\\"><font color=\\\"#ffffff\\\">"); // get only stat table data
		$wrpi_stats = substr($wrpi_stats, 0, (strripos($wrpi_stats, "</tr></table></center><table width=\\\"1000\\\">"))); 
		$wrpi_array = explode("</tr>",$wrpi_stats);
		
		
		
		
		$whfile = fopen( '/var/www/rpits/wrpi_mh_stats.txt', "w+");
		$today = date("l");
		fwrite($whfile,  date("l"));
		fwrite($whfile,  "\n");
		
		
		
		for($x = 1; $x <13 ; $x++  )
		{	
			$wrpi_array_test = explode("<td",$wrpi_array[$x] );	
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
			
			fwrite($whfile, $team_string);
			fwrite($whfile, ",");
			fwrite($whfile, $pts_string);
			fwrite($whfile, ",");
			fwrite($whfile, $conf_record_string);
			fwrite($whfile, ",");
			fwrite($whfile, $overall_record_string);
			fwrite($whfile, ",\n");
			$lines[] = array($team_string, $pts_string, $conf_record_string, $overall_record_string);
		}
	
	}
	
	$text = array();
	$text[0] = "Team\n";
	$text[1] = "Pts \n"; 
	$text[2] = "Conf \n"; 
	$text[3] = "Overall \n";
	for($x = 0; $x < 12; $x++)
	{
		if($x == 11)
		{
			$text[0] .= $lines[$x][0];
			$text[1] .= $lines[$x][1];
			$text[2] .= $lines[$x][2];
			$text[3] .= $lines[$x][3];
		}
		else
		{
			$text[0] .= $lines[$x][0];
			$text[1] .= $lines[$x][1];
			$text[2] .= $lines[$x][2];
			$text[3] .= $lines[$x][3];
			
			$text[0] .= "\n";
			$text[1] .= "\n";
			$text[2] .= "\n";
			$text[3] .= "\n";
		}
	}
	
	if($team_stat == "team")
	{
		return($text[0]);
	}
	elseif($team_stat == "pts")
	{
		return($text[1]);
	}
	elseif($team_stat == "conf")
	{
		return($text[2]);
	}
	elseif($team_stat == "overall")
	{
		return($text[3]);
	}
	elseif($team_stat == "1")
	{
		if($lines[0][0] == "St. Lawrence")
		{
			return(getToken("o.slu.logo",$eventId));
		}
		elseif($lines[0][0] == "Rensselaer")
		{
			return(getToken("o.rpi.logo",$eventId));
		}
		elseif($lines[0][0] == "Clarkson")
		{
			return(getToken("o.clark.logo",$eventId));
		}
		else
		{			
			return(getToken("o." . strtolower($lines[0][0]) . ".logo",$eventId));
		}
	}
	elseif($team_stat == "2")
	{
		if($lines[1][0] == "St. Lawrence")
		{
			return(getToken("o.slu.logo",$eventId));
		}
		elseif($lines[1][0] == "Rensselaer")
		{
			return(getToken("o.rpi.logo",$eventId));
		}
		elseif($lines[1][0] == "Clarkson")
		{
			return(getToken("o.clark.logo",$eventId));
		}
		else
		{			
			return(getToken("o." . strtolower($lines[1][0]) . ".logo",$eventId));
		}
	}
	elseif($team_stat == "3")
	{
		if($lines[2][0] == "St. Lawrence")
		{
			return(getToken("o.slu.logo",$eventId));
		}
		elseif($lines[2][0] == "Rensselaer")
		{
			return(getToken("o.rpi.logo",$eventId));
		}
		elseif($lines[2][0] == "Clarkson")
		{
			return(getToken("o.clark.logo",$eventId));
		}
		else
		{			
			return(getToken("o." . strtolower($lines[2][0]) . ".logo",$eventId));
		}
	}
	elseif($team_stat == "4")
	{
		if($lines[3][0] == "St. Lawrence")
		{
			return(getToken("o.slu.logo",$eventId));
		}
		elseif($lines[3][0] == "Rensselaer")
		{
			return(getToken("o.rpi.logo",$eventId));
		}
		elseif($lines[3][0] == "Clarkson")
		{
			return(getToken("o.clark.logo",$eventId));
		}
		else
		{			
			return(getToken("o." . strtolower($lines[3][0]) . ".logo",$eventId));
		}
	}
	elseif($team_stat == "5")
	{
		if($lines[4][0] == "St. Lawrence")
		{
			return(getToken("o.slu.logo",$eventId));
		}
		elseif($lines[4][0] == "Rensselaer")
		{
			return(getToken("o.rpi.logo",$eventId));
		}
		elseif($lines[4][0] == "Clarkson")
		{
			return(getToken("o.clark.logo",$eventId));
		}
		else
		{			
			return(getToken("o." . strtolower($lines[4][0]) . ".logo",$eventId));
		}
	}
	elseif($team_stat == "6")
	{
		if($lines[5][0] == "St. Lawrence")
		{
			return(getToken("o.slu.logo",$eventId));
		}
		elseif($lines[5][0] == "Rensselaer")
		{
			return(getToken("o.rpi.logo",$eventId));
		}
		elseif($lines[5][0] == "Clarkson")
		{
			return(getToken("o.clark.logo",$eventId));
		}
		else
		{			
			return(getToken("o." . strtolower($lines[5][0]) . ".logo",$eventId));
		}
	}
	elseif($team_stat == "7")
	{
		if($lines[6][0] == "St. Lawrence")
		{
			return(getToken("o.slu.logo",$eventId));
		}
		elseif($lines[6][0] == "Rensselaer")
		{
			return(getToken("o.rpi.logo",$eventId));
		}
		elseif($lines[6][0] == "Clarkson")
		{
			return(getToken("o.clark.logo",$eventId));
		}
		else
		{			
			return(getToken("o." . strtolower($lines[6][0]) . ".logo",$eventId));
		}
	}
	elseif($team_stat == "8")
	{
		if($lines[7][0] == "St. Lawrence")
		{
			return(getToken("o.slu.logo",$eventId));
		}
		elseif($lines[7][0] == "Rensselaer")
		{
			return(getToken("o.rpi.logo",$eventId));
		}
		elseif($lines[7][0] == "Clarkson")
		{
			return(getToken("o.clark.logo",$eventId));
		}
		else
		{			
			return(getToken("o." . strtolower($lines[7][0]) . ".logo",$eventId));
		}
	}
	elseif($team_stat == "9")
	{
		if($lines[8][0] == "St. Lawrence")
		{
			return(getToken("o.slu.logo",$eventId));
		}
		elseif($lines[8][0] == "Rensselaer")
		{
			return(getToken("o.rpi.logo",$eventId));
		}
		elseif($lines[8][0] == "Clarkson")
		{
			return(getToken("o.clark.logo",$eventId));
		}
		else
		{			
			return(getToken("o." . strtolower($lines[8][0]) . ".logo",$eventId));
		}
	}
	elseif($team_stat == "10")
	{
		if($lines[9][0] == "St. Lawrence")
		{
			return(getToken("o.slu.logo",$eventId));
		}
		elseif($lines[9][0] == "Rensselaer")
		{
			return(getToken("o.rpi.logo",$eventId));
		}
		elseif($lines[9][0] == "Clarkson")
		{
			return(getToken("o.clark.logo",$eventId));
		}
		else
		{			
			return(getToken("o." . strtolower($lines[9][0]) . ".logo",$eventId));
		}
	}
	elseif($team_stat == "11")
	{
		if($lines[10][0] == "St. Lawrence")
		{
			return(getToken("o.slu.logo",$eventId));
		}
		elseif($lines[10][0] == "Rensselaer")
		{
			return(getToken("o.rpi.logo",$eventId));
		}
		elseif($lines[10][0] == "Clarkson")
		{
			return(getToken("o.clark.logo",$eventId));
		}
		else
		{			
			return(getToken("o." . strtolower($lines[10][0]) . ".logo",$eventId));
		}
	}
	elseif($team_stat == "12")
	{
		if($lines[11][0] == "St. Lawrence")
		{
			return(getToken("o.slu.logo",$eventId));
		}
		elseif($lines[11][0] == "Rensselaer")
		{
			return(getToken("o.rpi.logo",$eventId));
		}
		elseif($lines[11][0] == "Clarkson")
		{
			return(getToken("o.clark.logo",$eventId));
		}
		else
		{			
			return(getToken("o." . strtolower($lines[11][0]) . ".logo",$eventId));
		}
	}
}
?>