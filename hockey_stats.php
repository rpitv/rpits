<?php

function hockey_stats($source,$eventId) {
	
	$livestats = loadXmlCached($source);
	$cache = array();
	
	/* Pull officials */
		$referees = $livestats->xpath("//official[@title='Referee']");
		$linesmen = $livestats->xpath("//official[@title='Linesman']");
		$cache["referee1"] = $referees[0]["name"];
		$cache["referee2"] = $referees[1]["name"];
		$cache["linesman1"] = $linesmen[0]["name"];
		$cache["linesman2"] = $linesmen[1]["name"];
		
		/* Pull Stats */
		$hgame = $livestats->xpath("//team[@vh = 'H']/linescore");
		$vgame = $livestats->xpath("//team[@vh = 'V']/linescore");
		$hperiods = $livestats->xpath("//team[@vh = 'H']/linescore/lineprd");
		$vperiods = $livestats->xpath("//team[@vh = 'V']/linescore/lineprd");
		if($hgame[0]["periods"] > 0)
		{
			$hscore = 0;
			$hshots = 0;
			$hsaves = 0;
			$hpen = 0;
			$hpen_min = 0;
			$hfowon = 0;
			for($x = 0; $x <= $hgame[0]["periods"] ; $x++)
			{
				$hscore += (int)$hperiods[$x]["score"];
				$hshots += (int)$hperiods[$x]["shots"];
				$hsaves += (int)$hperiods[$x]["saves"];
				$hpen += (int)$hperiods[$x]["pen"];
				$hpen_min += (int)$hperiods[$x]["pmin"];
				$hfowon += (int)$hperiods[$x]["fowon"];
			}
			$cache["hscore"] = $hscore;
			$cache["hshots"] = $hshots;
			$cache["hsaves"] = $hsaves;
			$cache["hpen"] = $hpen;
			$cache["hpen_min"] = $hpen_min;
			$cache["hfowon"] = $hfowon;
		}
		if($vgame[0]["periods"] > 0)
		{
			$vscore = 0;
			$vshots = 0;
			$vsaves = 0;
			$vpen = 0;
			$vpen_min = 0;
			$vfowon = 0;
			for($x = 0; $x <= $vgame[0]["periods"] ; $x++)
			{
				$vscore += (int)$vperiods[$x]["score"];
				$vshots += (int)$vperiods[$x]["shots"];
				$vsaves += (int)$vperiods[$x]["saves"];
				$vpen += (int)$vperiods[$x]["pen"];
				$vpen_min += (int)$vperiods[$x]["pmin"];
				$vfowon += (int)$vperiods[$x]["fowon"];
			}
			$cache["vscore"] = $vscore;
			$cache["vshots"] = $vshots;
			$cache["vsaves"] = $vsaves;
			$cache["vpen"] = $vpen;
			$cache["vpen_min"] = $vpen_min;
			$cache["vfowon"] = $vfowon;
		}
		
		/* Pull Players */
		$hteam = $livestats->xpath("//team[@vh = 'H']/lines/line");
		$vteam = $livestats->xpath("//team[@vh = 'V']/lines/line");
		$hfullteam = $livestats->xpath("//team[@vh = 'H']/player");
		$vfullteam = $livestats->xpath("//team[@vh = 'V']/player");
		
		$hplayers = array();
		$vplayers = array();
		
		for($x = 0; $x < count($hfullteam) ; $x++)
		{
			$name_split = explode(" ", $hfullteam[$x]["name"]);
			$part_1 = $name_split[1];
			$part_2 = $name_split[0];
			$hplayers[(int)$hfullteam[$x]["code"]] = $part_2 . " " . $part_1; 
		}
		for($x = 0; $x < count($vfullteam) ; $x++)
		{
			if (strpos($vfullteam[$x]["name"],',') !== false)
			{
				$name_split = explode(",", $vfullteam[$x]["name"]);
				$part_1 = (string)ucfirst(strtolower((string)$name_split[0]));
				$part_2 = (string)ucfirst(strtolower((string)$name_split[1]));
				$vplayers[(int)$vfullteam[$x]["code"]] = $name_split[1] . " " . $name_split[0];
			}
			else
			{
				$name_split = explode(" ", $vfullteam[$x]["name"]);
				$part_1 = $name_split[1];
				$part_2 = $name_split[0];
				$vplayers[(int)$vfullteam[$x]["code"]] = $part_2 . " " . $part_1; 
			}			
		}
		/*Home*/
		/* First Line */
		$cache["hlw1"] = $hteam[0]["lw"] . " - " . $hplayers[(int)$hteam[0]["lw"]];
		$cache["hlw1_pic"] = str_replace(' ', '', $hplayers[(int)$hteam[0]["lw"]]);
		$cache["hc1"] = $hteam[0]["c"] . " - " . $hplayers[(int)$hteam[0]["c"]];
		$cache["hc1_pic"] = str_replace(' ', '', $hplayers[(int)$hteam[0]["c"]]);
		$cache["hrw1"] = $hteam[0]["rw"] . " - " . $hplayers[(int)$hteam[0]["rw"]];
		$cache["hrw1_pic"] = str_replace(' ', '', $hplayers[(int)$hteam[0]["rw"]]);
		$cache["hld1"] = $hteam[0]["ld"] . " - " . $hplayers[(int)$hteam[0]["ld"]];
		$cache["hld1_pic"] = str_replace(' ', '', $hplayers[(int)$hteam[0]["ld"]]);
		$cache["hrd1"] = $hteam[0]["rd"] . " - " . $hplayers[(int)$hteam[0]["rd"]];
		$cache["hrd1_pic"] = str_replace(' ', '', $hplayers[(int)$hteam[0]["rd"]]);
		$cache["hg1"] = $hteam[0]["g"] . " - " . $hplayers[(int)$hteam[0]["g"]];
		$cache["hg1_pic"] = str_replace(' ', '', $hplayers[(int)$hteam[0]["g"]]);
		/* Second Line */
		$cache["hlw2"] = $hteam[1]["lw"] . " - " . $hplayers[(int)$hteam[1]["lw"]];
		$cache["hlw2_pic"] = str_replace(' ', '', $hplayers[(int)$hteam[1]["lw"]]);
		$cache["hc2"] = $hteam[1]["c"] . " - " . $hplayers[(int)$hteam[1]["c"]];
		$cache["hc2_pic"] = str_replace(' ', '', $hplayers[(int)$hteam[1]["c"]]);
		$cache["hrw2"] = $hteam[1]["rw"] . " - " . $hplayers[(int)$hteam[1]["rw"]];
		$cache["hrw2_pic"] = str_replace(' ', '', $hplayers[(int)$hteam[1]["rw"]]);
		$cache["hld2"] = $hteam[1]["ld"] . " - " . $hplayers[(int)$hteam[1]["ld"]];
		$cache["hld2_pic"] = str_replace(' ', '', $hplayers[(int)$hteam[1]["ld"]]);
		$cache["hrd2"] = $hteam[1]["rd"] . " - " . $hplayers[(int)$hteam[1]["rd"]];
		$cache["hrd2_pic"] = str_replace(' ', '', $hplayers[(int)$hteam[1]["rd"]]);
		$cache["hg2"] = $hteam[1]["g"] . " - " . $hplayers[(int)$hteam[1]["g"]];
		$cache["hg2_pic"] = str_replace(' ', '', $hplayers[(int)$hteam[1]["g"]]);
		/* Third Line */
		$cache["hlw3"] = $hteam[2]["lw"] . " - " . $hplayers[(int)$hteam[2]["lw"]];
		$cache["hlw3_pic"] = str_replace(' ', '', $hplayers[(int)$hteam[2]["lw"]]);
		$cache["hc3"] = $hteam[2]["c"] . " - " . $hplayers[(int)$hteam[2]["c"]];
		$cache["hc3_pic"] = str_replace(' ', '', $hplayers[(int)$hteam[2]["c"]]);
		$cache["hrw3"] = $hteam[2]["rw"] . " - " . $hplayers[(int)$hteam[2]["rw"]];
		$cache["hrw3_pic"] = str_replace(' ', '', $hplayers[(int)$hteam[2]["rw"]]);
		$cache["hld3"] = $hteam[2]["ld"] . " - " . $hplayers[(int)$hteam[2]["ld"]];
		$cache["hld3_pic"] = str_replace(' ', '', $hplayers[(int)$hteam[2]["ld"]]);
		$cache["hrd3"] = $hteam[2]["rd"] . " - " . $hplayers[(int)$hteam[2]["rd"]];
		$cache["hrd3_pic"] = str_replace(' ', '', $hplayers[(int)$hteam[2]["rd"]]);
		$cache["hg3"] = $hteam[2]["g"] . " - " . $hplayers[(int)$hteam[2]["g"]];
		$cache["hg3_pic"] = str_replace(' ', '', $hplayers[(int)$hteam[2]["g"]]);
		/* Fourth Line */
		$cache["hlw4"] = $hteam[3]["lw"] . " - " . $hplayers[(int)$hteam[3]["lw"]];
		$cache["hlw4_pic"] = str_replace(' ', '', $hplayers[(int)$hteam[3]["lw"]]);
		$cache["hc4"] = $hteam[3]["c"] . " - " . $hplayers[(int)$hteam[3]["c"]];
		$cache["hc4_pic"] = str_replace(' ', '', $hplayers[(int)$hteam[3]["c"]]);
		$cache["hrw4"] = $hteam[3]["rw"] . " - " . $hplayers[(int)$hteam[3]["rw"]];
		$cache["hrw4_pic"] = str_replace(' ', '', $hplayers[(int)$hteam[3]["rw"]]);
		$cache["hld4"] = $hteam[3]["ld"] . " - " . $hplayers[(int)$hteam[3]["ld"]];
		$cache["hld4_pic"] = str_replace(' ', '', $hplayers[(int)$hteam[3]["ld"]]);
		$cache["hrd4"] = $hteam[3]["rd"] . " - " . $hplayers[(int)$hteam[3]["rd"]];	
		$cache["hrd4_pic"] = str_replace(' ', '', $hplayers[(int)$hteam[3]["rd"]]);
		$cache["hg4"] = $hteam[3]["g"] . " - " . $hplayers[(int)$hteam[3]["g"]];
		$cache["hg4_pic"] =str_replace(' ', '', $hplayers[(int)$hteam[3]["g"]]);
		
		$cache["hg1_stats"] = getToken("e.h.p.num.".$hteam[0]["g"].".s2",$eventId)."-".getToken("e.h.p.num.".$hteam[0]["g"].".s3",$eventId)."-".getToken("e.h.p.num.".$hteam[0]["g"].".s4",$eventId)."\n".getToken("e.h.p.num.".$hteam[0]["g"].".s6",$eventId)."\n".getToken("e.h.p.num.".$hteam[0]["g"].".s5",$eventId);
		
		/*Away*/
		/* First Line */
		$cache["vlw1"] = $vteam[0]["lw"] . " - " . $vplayers[(int)$vteam[0]["lw"]];
		$cache["vlw1_pic"] = str_replace(' ', '', $vplayers[(int)$vteam[0]["lw"]]);
		$cache["vc1"] = $vteam[0]["c"] . " - " . $vplayers[(int)$vteam[0]["c"]];
		$cache["vc1_pic"] = str_replace(' ', '', $vplayers[(int)$vteam[0]["c"]]);
		$cache["vrw1"] = $vteam[0]["rw"] . " - " . $vplayers[(int)$vteam[0]["rw"]];
		$cache["vrw1_pic"] = str_replace(' ', '', $vplayers[(int)$vteam[0]["rw"]]);
		$cache["vld1"] = $vteam[0]["ld"] . " - " . $vplayers[(int)$vteam[0]["ld"]];
		$cache["vld1_pic"] = str_replace(' ', '', $vplayers[(int)$vteam[0]["ld"]]);
		$cache["vrd1"] = $vteam[0]["rd"] . " - " . $vplayers[(int)$vteam[0]["rd"]];
		$cache["vrd1_pic"] = str_replace(' ', '', $vplayers[(int)$vteam[0]["rd"]]);
		$cache["vg1"] = $vteam[0]["g"] . " - " . $vplayers[(int)$vteam[0]["g"]];
		$cache["vg1_pic"] = str_replace(' ', '', $vplayers[(int)$vteam[0]["g"]]);
		/* Second Line */
		$cache["vlw2"] = $vteam[1]["lw"] . " - " . $vplayers[(int)$vteam[1]["lw"]];
		$cache["vlw2_pic"] = str_replace(' ', '', $vplayers[(int)$vteam[1]["lw"]]);
		$cache["vc2"] = $vteam[1]["c"] . " - " . $vplayers[(int)$vteam[1]["c"]];
		$cache["vc2_pic"] = str_replace(' ', '', $vplayers[(int)$vteam[1]["c"]]);
		$cache["vrw2"] = $vteam[1]["rw"] . " - " . $vplayers[(int)$vteam[1]["rw"]];
		$cache["vrw2_pic"] = str_replace(' ', '', $vplayers[(int)$vteam[1]["rw"]]);
		$cache["vld2"] = $vteam[1]["ld"] . " - " . $vplayers[(int)$vteam[1]["ld"]];
		$cache["vld2_pic"] = str_replace(' ', '', $vplayers[(int)$vteam[1]["ld"]]);
		$cache["vrd2"] = $vteam[1]["rd"] . " - " . $vplayers[(int)$vteam[1]["rd"]];
		$cache["vrd2_pic"] = str_replace(' ', '', $vplayers[(int)$vteam[1]["rd"]]);
		$cache["vg2"] = $vteam[1]["g"] . " - " . $vplayers[(int)$vteam[1]["g"]];
		$cache["vg2_pic"] = str_replace(' ', '', $vplayers[(int)$vteam[1]["g"]]);
		/* Third Line */
		$cache["vlw3"] = $vteam[2]["lw"] . " - " . $vplayers[(int)$vteam[2]["lw"]];
		$cache["vlw3_pic"] = str_replace(' ', '', $vplayers[(int)$vteam[2]["lw"]]);
		$cache["vc3"] = $vteam[2]["c"] . " - " . $vplayers[(int)$vteam[2]["c"]];
		$cache["vc3_pic"] = str_replace(' ', '', $vplayers[(int)$vteam[2]["c"]]);
		$cache["vrw3"] = $vteam[2]["rw"] . " - " . $vplayers[(int)$vteam[2]["rw"]];
		$cache["vrw3_pic"] = str_replace(' ', '', $vplayers[(int)$vteam[2]["rw"]]);
		$cache["vld3"] = $vteam[2]["ld"] . " - " . $vplayers[(int)$vteam[2]["ld"]];
		$cache["vld3_pic"] = str_replace(' ', '', $vplayers[(int)$vteam[2]["ld"]]);
		$cache["vrd3"] = $vteam[2]["rd"] . " - " . $vplayers[(int)$vteam[2]["rd"]];
		$cache["vrd3_pic"] = str_replace(' ', '', $vplayers[(int)$vteam[2]["rd"]]);
		$cache["vg3"] = $vteam[2]["g"] . " - " . $vplayers[(int)$vteam[2]["g"]];
		$cache["vg3_pic"] = str_replace(' ', '', $vplayers[(int)$vteam[2]["g"]]);
		/* Fourth Line */
		$cache["vlw4"] = $vteam[3]["lw"] . " - " . $vplayers[(int)$vteam[3]["lw"]];
		$cache["vlw4_pic"] = str_replace(' ', '', $vplayers[(int)$vteam[3]["lw"]]);
		$cache["vc4"] = $vteam[3]["c"] . " - " . $vplayers[(int)$vteam[3]["c"]];
		$cache["vc4_pic"] = str_replace(' ', '', $vplayers[(int)$vteam[3]["c"]]);
		$cache["vrw4"] = $vteam[3]["rw"] . " - " . $vplayers[(int)$vteam[3]["rw"]];
		$cache["vrw4_pic"] = str_replace(' ', '', $vplayers[(int)$vteam[3]["rw"]]);
		$cache["vld4"] = $vteam[3]["ld"] . " - " . $vplayers[(int)$vteam[3]["ld"]];
		$cache["vld4_pic"] = str_replace(' ', '', $vplayers[(int)$vteam[3]["ld"]]);
		$cache["vrd4"] = $vteam[3]["rd"] . " - " . $vplayers[(int)$vteam[3]["rd"]];	
		$cache["vrd4_pic"] = str_replace(' ', '', $vplayers[(int)$vteam[3]["rd"]]);
		$cache["vg4"] = $vteam[3]["g"] . " - " . $vplayers[(int)$vteam[3]["g"]];
		$cache["vg4_pic"] =str_replace(' ', '', $vplayers[(int)$vteam[3]["g"]]);
		
		$cacve["vg1_stats"] = getToken("e.v.p.num.".$vteam[0]["g"].".record",$eventId)."\n".getToken("e.v.p.num.".$vteam[0]["g"].".s6",$eventId)."\n".getToken("e.v.p.num.".$vteam[0]["g"].".s5",$eventId);
		
		/* Pull Random Stats */
		$venue_stats = $livestats->xpath("//venue");
		
		$cache["attendance"] = $venue_stats[0]["attend"]; 
		$cache["hid"] = $venue_stats[0]["homeid"];
		$cache["vid"] = $venue_stats[0]["visid"];
		$cache["date"] = $venue_stats[0]["date"];
		
		/* Pull Penalties */
		$penalties = $livestats->xpath("//pen");
		$h = false;
		$v = false;
		for($x = count($penalties)-1; $x >= 0 ; $x--)
		{
			$name = $penalties[$x]["name"];
			$player = $livestats->xpath('//player[@name= "' . $name . '"]');
			if($penalties[$x]["vh"] == "H" && !$h)
			{
				
				$h = true;
				if (strpos($penalties[$x]["name"],',') !== false)
				{
					$name_split = explode(",", $penalties[$x]["name"]);
					$part_1 = (string)ucfirst(strtolower((string)$name_split[0]));
					$part_2 = (string)ucfirst(strtolower((string)$name_split[1]));
					$cache["hcpen"] = $player[0]["uni"] . " - " . $name_split[1] . " " . $name_split[0];
					$cache["hcpentype"] = ucfirst(strtolower($penalties[$x]["desc"]));
				}
				else
				{
					$name_split = explode(" ", $penalties[$x]["name"]);
					$part_1 = $name_split[1];
					$part_2 = $name_split[0];
					$cache["hcpen"] = $player[0]["uni"] . " - " . $part_2 . " " . $part_1;					
					$cache["hcpentype"] = ucfirst(strtolower($penalties[$x]["desc"]));
				}	
			}
			if($penalties[$x]["vh"] == "V" && !$v)
			{
				$v = true;
				if (strpos($penalties[$x]["name"],',') !== false)
				{
					$name_split = explode(",", $penalties[$x]["name"]);
					$part_1 = (string)ucfirst(strtolower((string)$name_split[0]));
					$part_2 = (string)ucfirst(strtolower((string)$name_split[1]));
					$cache["vcpen"] = $player[0]["uni"] . " - " . $name_split[1] . " " . $name_split[0];
					$cache["vcpentype"] = ucfirst(strtolower($penalties[$x]["desc"]));
				}
				else
				{
					$name_split = explode(" ", $penalties[$x]["name"]);
					$part_1 = $name_split[1];
					$part_2 = $name_split[0];
					$cache["vcpen"] = $player[0]["uni"] . " - " . $part_2 . " " . $part_1; 
					$cache["vcpentype"] = ucfirst(strtolower($penalties[$x]["desc"]));
				}	
			}
		}
	
	
	return $cache;
}
?>