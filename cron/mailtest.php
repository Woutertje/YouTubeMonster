<?php
	# setup
	$t = strtotime('now') + microtime();
	include('../core/config.php');
	$GLOBALS['config']['classesroot'] = '../classes/';
	$GLOBALS['config']['templateroot'] = '../templates/';
	include('../core/startup.php');
	
	# Globals
	global $db;
	
	# Reset todays ratings
	$mail = new templatemail('just testing', 'test', array(
		'email' => 'user'
	));
	if($mail->send())
		echo 'Mail sent.';
	else
		echo 'Mail not sent.';