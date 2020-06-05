<?php

//
// Removes the templates table.
//
// The template column of titles becomes 'parent', and can have either a title
// ID or a path to an XML file
//
// Filename column is removed, as file names become implicit
//
// Templates table is dropped
//

$includePath = '../';
include($includePath . 'include.php');

$mysqli->select_db("rpits");

echo '<pre>';

$checkData = dbQuery("SELECT * FROM `titles` LIMIT 1");
$check = $checkData->fetch_assoc();

if(isset($check['template'])) {
	$sql = 'ALTER TABLE `titles` CHANGE  `template`  `parent` VARCHAR( 255 ) NOT NULL';
	echo $sql . "\n\n";
	dbQuery($sql);
	$sql = 'ALTER TABLE `titles` DROP `filename`';
	echo $sql . "\n\n";
	dbQuery($sql);

	$templateData = dbQuery('SELECT * FROM templates');
	$templates = array();
	while($template = $templateData->fetch_assoc()) {
		$templates[$template['id']] = $template;
	}

	$titleData = dbQuery('SELECT * FROM titles');
	$titles = array();
	while($title = $titleData->fetch_assoc()) {
		$titles[$title['id']] = $title;
		if(is_numeric($title['parent'])) {
			$sql = 'UPDATE titles SET `parent`="' . $templates[$title['parent']]['path'] . '" WHERE `id`=' . $title['id'];
			dbQuery($sql);

			echo $sql . "\n";
		}
	}

	$sql = 'DROP TABLE templates';
	echo "\n" . $sql . "\n";
	dbQuery($sql);
} else {
	echo 'Migration has alredy been run.';
}





?>
