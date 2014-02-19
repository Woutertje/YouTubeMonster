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
	function debug($msg){
		echo $msg.'<br />';
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