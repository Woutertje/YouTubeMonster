<?php
	# Keep time
	$t = strtotime('now') + microtime();
	
	if(empty($_POST['username']))
		die('No username given...');
	if(empty($_POST['password']))
		die('No username given...');
		
	# setup
	include('../core/config.php');
	$GLOBALS['config']['classesroot'] = '../classes/';
	include('../core/startup.php');
	
	# Delete old blocks
	$db->query('
		DELETE FROM `loginerrors`
		WHERE `time` < ?
	', array(
		strtotime('-5 mins')
	));
	
	# Grab users info
	$ip = $_SERVER['REMOTE_ADDR'];
	
	function setlogin($token, $remember){
		$_SESSION['login'] = $token;
		if($remember == 'yes')
			setcookie('me', $token, strtotime('+1 year'), '/');
		die('success');
	}
	
	# Check login attampts
	if($db->query('
		SELECT `time`
		FROM `loginerrors`
		WHERE `ip` = ?
		&& `username` = ?
	', array(
		$ip,
		$_POST['username']
	))->rowCount() > 4){
		$timeleft = $db->query('
			SELECT `time`
			FROM `loginerrors`
			WHERE `ip` = ?
			&& `username` = ?
			ORDER BY `time` ASC
			LIMIT 1
		', array(
			$ip,
			$_POST['username']
		))->fetchColumn();
		die('Too many invalid login attampts. You may try again in '.($timeleft - (strtotime('now') - 300)).' seconds.');
	}
	else{
		$getlogin = $db->query('
			SELECT `id`, `token`, `password`, `activated`, `activatekey`
			FROM `users`
			WHERE `username` = ?
			LIMIT 1
		', array(
			$_POST['username']
		))->fetch();
		if($getlogin != null){
			$crypter = new encryption();
			if($crypter->safepass($_POST['password'], $_POST['username']) == $getlogin['password']){
				if($getlogin['activated'] == 0){
					if($_POST['activatekey'] == $getlogin['activatekey']){
						$db->update('users', array(
							'activated' => 1,
							'activatekey' => ''
						), array(
							'token' => $getlogin['token']
						));
						logmsg('Registration of "'.$_POST['username'].'" is complete, Welcome!', 'registration', $getlogin['id']);
						setlogin($getlogin['token'], $_POST['remember']);
					}
					else{
						die('activatetoken');
					}
				}
				else{
					setlogin($getlogin['token'], $_POST['remember']);
				}
			}
			else
				echo 'Invalid password.';
		}
		else
			echo 'Username not found.';
		$db->insert('loginerrors', array(
			'ip' => $ip,
			'username' => $_POST['username'],
			'time' => strtotime('now')
		));
	}