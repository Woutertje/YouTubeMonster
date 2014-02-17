<?php
	# setup
	$t = strtotime('now') + microtime();
	include('../core/config.php');
	$GLOBALS['config']['classesroot'] = '../classes/';
	include('../core/startup.php');
	
	# Globals
	global $db;
	
	# Reset todays ratings
	$db->query('
		UPDATE `videos`
		SET `scoretoday` = 0
	');
	$db->query('
		TRUNCATE TABLE `rates`
	');
	
	# Finish
	echo 'Done.';