<?php
	# Keep time
	$t = strtotime('now') + microtime();
	
	# Default error checks
	if(empty($_POST['username']))
		die('No username given...');
	if(empty($_POST['email']))
		die('No email given...');
	if(!preg_match("/^[A-Z0-9._%-]+@[A-Z0-9][A-Z0-9.-]{0,61}[A-Z0-9]\.[A-Z]{2,6}$/i", $_POST['email']))
		die('Your email address is not valid.');
	if(empty($_POST['password']))
		die('No password given...');
	if(empty($_POST['password2']))
		die('Password was not repeated...');
	if(strlen($_POST['password']) < 6)
		die('Your password must at least have 5 characters.');
	if($_POST['password'] != $_POST['password2'])
		die('Passwords do not match!');
	if($_POST['username'] != preg_replace('/[^a-zA-Z0-9]/', '~|~', $_POST['username']))
		die('Invalid username, only use A-Z, a-z and 0-9. No spaces.');
	if(strlen($_POST['username']) < 4)
		die('Your username must at least have 3 characters.');
	if(strlen($_POST['username']) > 15)
		die('Your username can\'t have more than 15 characters.');
		
	# setup
	include('../core/config.php');
	$GLOBALS['config']['classesroot'] = '../classes/';
	$GLOBALS['config']['templateroot'] = '../templates/';
	include('../core/startup.php');
	
	# Set globals
	global $db;
	
	# Remove accounts who do not activate within 7 days
	foreach($db->query('
		SELECT `id`, `username`
		FROM `users`
		WHERE `activated` = 0
		AND `registeredat` < ?
	', array(
		strtotime('-1 week')
	)) as $removeme){
		logmsg('Registration of "'.$removeme['username'].'" expired, removing account.', 'registration', 'system');
		$db->delete('users', array(
			'id' => $removeme['id']
		));
	}
	
	# Database field checks
	if($db->query('
		SELECT COUNT(*)
		FROM `users`
		WHERE `username` = ?
	', array(
		$_POST['username']
	))->fetchColumn() > 0)
		die('That username is already in use!');
		
	if($db->query('
		SELECT COUNT(*)
		FROM `users`
		WHERE `email` = ?
	', array(
		$_POST['email']
	))->fetchColumn() > 0)
		die('That email is already in use! Recover account using "lost username".');
		
	# Winning! Create account!
	$encoder = new encryption();
	$activatekey = $encoder->generatetoken(5, '0123456789');
	$token = $encoder->generatetoken();
	while($GLOBALS['db']->query('
		SELECT `id`
		FROM `users`
		WHERE `token` = ?
	', array(
		$token
	))->rowCount() > 0)
		$token = $encoder->generatetoken();
	$mail = new templatemail('
		Hi '.htmlspecialchars($_POST['username']).'!<br><br>
		You have created an account on YouTubeMonster. All you need to do now is log in! For your fist login you\'ll need an activation code, which you will find below:<br><br>
		Username: '.htmlspecialchars($_POST['username']).'<br>
		Your activation code: <b>'.$activatekey.'</b><br><br>
		If you do not activate your account by loggin in within 7 days, your account will be removed so your username is available to others.<br><br>
		We hope to give you the best entertainment!<br><br>
		Yours faithfully,<br><br>
		'.$GLOBALS['domaintitle'].'
	', 'Registration '.$_POST['username'].', activation key', array(
		$_POST['email'] => htmlspecialchars($_POST['name'])
	));
	$mail->send();
	# Insert user
	$db->insert('users', array(
		'username' => $_POST['username'],
		'password' => $encoder->safepass($_POST['password'], $_POST['username']),
		'email' => $_POST['email'],
		'activatekey' => $activatekey,
		'registeredat' => strtotime('now'),
		'token' => $token
	));
	logmsg('A new user ("'.$_POST['username'].'") registered to the website.', 'registration', $db->lastinsertid());
	die('success');