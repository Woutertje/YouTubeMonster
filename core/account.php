<?php
	global $db, $loggedin, $curuser;

	# Check if user is loggedin
	if(!empty($_SESSION['login'])){
		// check if user exists
		$curuser = $db->query('
			SELECT *
			FROM `users`
			WHERE `token` = ?
		', array(
			$_SESSION['login']
		))->fetch();
		if($GLOBALS['curuser'] == null)
			$loggedin = false;
		else
			$loggedin = true;
	}
	if(!$loggedin && !empty($_COOKIE['me'])){
		$curuser = $db->query('
			SELECT *
			FROM `users`
			WHERE `token` = ?
		', array(
			$_COOKIE['me']
		))->fetch();
		if($GLOBALS['curuser'] == null){
			$loggedin = false;
			setcookie('me', '', 0, '/');
		}
		else
			$loggedin = true;
	}