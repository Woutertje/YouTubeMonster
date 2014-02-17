<?php
	# Keep time
	$t = strtotime('now') + microtime();
	
	# Default error checks
	// die on failure
		
	# setup
	include('../core/config.php');
	$GLOBALS['config']['classesroot'] = '../classes/';
	$GLOBALS['config']['templateroot'] = '../templates/';
	include('../core/startup.php');
	include('../core/account.php');
	
	# Set globals
	global $db, $loggedin, $curuser;
	
	# Login check
	if(!$loggedin)
		die('You need to be logged in to report a video.');