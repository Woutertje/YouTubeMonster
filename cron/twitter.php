<?php

	# ==========================
	# CONFIG
	# ==========================
	
	$check_amount_of_new_vids = 5;
	
	# ==========================
	# Youtube RIP core
	# ==========================
	
	$t = strtotime('now') + microtime();
	include('../core/config.php');
	$GLOBALS['config']['classesroot'] = '../classes/';
	$GLOBALS['config']['templateroot'] = '../templates/';
	$GLOBALS['config']['pagesroot'] = '../pages/';
	include('../core/startup.php');
	
	function debug($str){
		echo $str.'<br />';
	}
	
	debug('Twitter testing');
	
	# Twitter setup
	include('../classes/OAuth.php');
	include('../classes/twitter.php');
	
	global $config;
	
	$twitter = new Twitter(
		$config['twitter']['consumerKey'],
		$config['twitter']['consumerSecret'],
		$config['twitter']['accessToken'],
		$config['twitter']['accessTokenSecret']
	);
	if(!$twitter->authenticate())
		die('Invalid twitter name or password');
	debug('Twitter account connected.');
	
	# Spam on twitter
	try{
		$twitter->send('test');
		debug('Spammed on twitter!');
	}
	catch(TwitterException $e){
		debug('<span style="color: #D00000;">Did not post on twitter: '.$e->getMessage().'</span>');
	}
	
	# Ending.
	debug('');
	debug('Ended ('.number_format((((strtotime('now') + microtime()) - $t) * 1000), 2, '.', '').' ms).');