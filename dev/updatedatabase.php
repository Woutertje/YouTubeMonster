<?php
	# setup
	$t = strtotime('now') + microtime();
	include('../core/config.php');
	$GLOBALS['config']['classesroot'] = '../classes/';
	include('../core/startup.php');
	global $db;
	
	# ==================================
	# Debug outcome
	# ==================================
	function debug($msg, $color = '#000'){
		echo '<div style="color: '.$color.'">'.$msg.'</div>';
	}
	
	# ==================================
	# Building the database from the
	# /db/ files.
	# ==================================
	debug('=== Database patcher ===');
	$db->query('
		CREATE TABLE IF NOT EXISTS `dbupdate` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`file` varchar(255) NOT NULL,
			`updatedat` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
	');
	debug('Created `dbupdate` table if it didn\'t excist yet.');
	
	# Fetch list of inserted database files
	$insertedfiles = array();
	foreach($db->query('
		SELECT `file`
		FROM `dbupdate`
	') as $insertedfile)
		$insertedfiles[] = $insertedfile['file'];
	
	# ==================================
	# Loop trough the database files and
	# insert new files into the database
	# ==================================
	debug('=== Patching ===');
	foreach(scandir('../db/') as $file){
		if(end(explode('.', $file)) != 'sql')
			continue;
			
		if(in_array($file, $insertedfiles)){
			debug($file, '#ddd');
		}
		else{
			$db->query(file_get_contents('../db/'.$file));
			$db->insert('dbupdate', array(
				'file' => $file
			));
			debug($file, '#00A000');
		}
    }
	
	# ==================================
	# Finish
	# ==================================
	debug('=== DB patched ===');
	debug('Updating took '.number_format((((strtotime('now') + microtime()) - $t) * 1000), 0, '.', '').' miliseconds.');