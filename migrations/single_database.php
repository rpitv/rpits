<?php

//

$includePath = '../';
include($includePath . 'include.php');

$oldName = 'rpihockey';
$newName = 'rpits';


mysql_select_db($newName);

echo '<pre>';

$check = dbQuery("SHOW TABLES LIKE `players`");

if(mysql_num_rows($check) > 0) {
	$copyPlayers = "CREATE  TABLE  `$newName`.`players` (  `id` int( 11  )  NOT  NULL  AUTO_INCREMENT ,
			`num` int( 2  )  DEFAULT NULL ,
			`first` varchar( 15  )  NOT  NULL ,
			`last` varchar( 20  )  NOT  NULL ,
			`pos` varchar( 3  )  NOT  NULL ,
			`height` varchar( 4  )  NOT  NULL ,
			`weight` varchar( 3  )  NOT  NULL ,
			`year` varchar( 2  )  NOT  NULL ,
			`hometown` varchar( 35  )  NOT  NULL ,
			`stype` varchar( 5  )  NOT  NULL ,
			`s1` varchar( 255  )  NOT  NULL ,
			`s2` varchar( 255  )  NOT  NULL ,
			`s3` varchar( 6  )  NOT  NULL ,
			`s4` varchar( 6  )  NOT  NULL ,
			`s5` varchar( 6  )  NOT  NULL ,
			`s6` varchar( 6  )  NOT  NULL ,
			`s7` varchar( 6  )  NOT  NULL ,
			`s8` varchar( 1023  )  NOT  NULL ,
			`team` varchar( 15  )  NOT  NULL ,
			PRIMARY  KEY (  `id`  )  ) ENGINE  =  MyISAM  DEFAULT CHARSET  = utf8 ROW_FORMAT  =  DYNAMIC ;

		 SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';

		 INSERT INTO `$newName`.`players` SELECT * FROM `$oldName`.`players`;";
	echo $copyPlayers;
	dbquery($copyPlayers);

	$copyStatType = "CREATE  TABLE  `$newName`.`stattype` (  `id` int( 11  )  NOT  NULL  AUTO_INCREMENT ,
			`type` varchar( 5  )  NOT  NULL ,
			`l1` varchar( 255  )  NOT  NULL ,
			`l2` varchar( 10  )  NOT  NULL ,
			`l3` varchar( 10  )  NOT  NULL ,
			`l4` varchar( 100  )  NOT  NULL ,
			`l5` varchar( 10  )  NOT  NULL ,
			`l6` varchar( 10  )  NOT  NULL ,
			`l7` varchar( 10  )  NOT  NULL ,
			`l8` varchar( 10  )  NOT  NULL ,
			`spacing` int( 11  )  NOT  NULL ,
			PRIMARY  KEY (  `id`  ) ,
			UNIQUE  KEY  `type` (  `type`  )  ) ENGINE  =  MyISAM  DEFAULT CHARSET  = utf8;

		 SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';

		 INSERT INTO `$newName`.`stattype` SELECT * FROM `$oldName`.`stattype`;";
	echo "\n\n" . $copyStatType;
	dbquery($copyStatType);

	$copyTeams = "CREATE  TABLE  `$newName`.`statscard_teams` (  `id` int( 11  )  NOT  NULL  AUTO_INCREMENT ,
			`name` varchar( 10  )  NOT  NULL ,
			`colorr` int( 3  )  NOT  NULL ,
			`colorg` int( 3  )  NOT  NULL ,
			`colorb` int( 3  )  NOT  NULL ,
			`logo` varchar( 14  )  NOT  NULL ,
			`logor` int( 3  )  NOT  NULL ,
			`logog` int( 3  )  NOT  NULL ,
			`logob` int( 3  )  NOT  NULL ,
			`start` int( 3  )  NOT  NULL ,
			`end` int( 3  )  NOT  NULL ,
			`womens` tinyint( 1  )  NOT  NULL ,
			`statsid` varchar( 255  )  NOT  NULL ,
			`hidden` int( 1  )  NOT  NULL ,
			PRIMARY  KEY (  `id`  ) ,
			KEY  `id` (  `id`  )  ) ENGINE  =  MyISAM  DEFAULT CHARSET  = utf8;

		 SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';

		 INSERT INTO `$newName`.`statscard_teams` SELECT * FROM `$oldName`.`teams`;";
	echo "\n\n" . $copyTeams;
	dbquery($copyTeams);
}
?>
