<?php
	# ===============================
	# GLOBALS
	# ===============================
	session_start();
	setlocale(LC_TIME, 'nld_nld');
	date_default_timezone_set('Europe/Amsterdam');
	global $db, $config;
	
	# ===============================
	# AUTOLOADER
	# ===============================
	function loadclass($name)
	{
		global $config;
		
		if (strpos($name, 'Page') > 0) {
			$file = $config['root'].'pages/' . $name . '.php';
		} else {
			$file = $config['root'].'classes/' . $name . '.php';
		}
	
		if (!file_exists($file)) {
			die('<b>[FATAL ERROR]</b> Class ' . $name . ' (' . $file . ') was not found.');
		}
		
		require_once $file;
	}
	spl_autoload_register('loadclass');
	
	# ===============================
	# MAIN DATABASE CONNECTION
	# ===============================
	try{
		$db = new database(
			$config['database']['host'],
			$config['database']['user'],
			$config['database']['pass'],
			$config['database']['database']
		);
	}
	catch(Exception $e){
		die('<b>core file</b> startup: [ERROR] Database was not able to connect. '.$e->getMessage());
	}
	
	# ===============================
	# Fuck you magic quotes
	# ===============================
	if(get_magic_quotes_gpc()){
		function stripslashes_gpc(&$value){
			$value = stripslashes($value);
		}
		array_walk_recursive($_GET, 'stripslashes_gpc');
		array_walk_recursive($_POST, 'stripslashes_gpc');
		array_walk_recursive($_COOKIE, 'stripslashes_gpc');
	}
	
	# ===============================
	# LOGBOOK MESSAGES
	# ===============================
	function logmsg($message, $category = '', $user_id = null){
		global $db, $loggedin, $curuser;
		
		if(strtolower($user_id) == 'system')
			$user_id = 13;
		else if($user_id == null && $loggedin)
			$user_id = $curuser['id'];
		
		$db->insert('logbook', array(
			'user_id' => $user_id,
			'message' => $message,
			'category' => $category,
			'time' => strtotime('now'),
			'ip' => $_SERVER['REMOTE_ADDR']
		));
	}
	
	# ===============================
	# GLOBAL JS/CSS ADD FUNCTIONS
	# ===============================
	function addjs($file){
		$GLOBALS['jsfiles'][] = array('file' => $file);
	}
	function addcss($file){
		$GLOBALS['cssfiles'][] = array('file' => $file);
	}
	
	# ===============================
	# Error, info & success messages
	# ===============================
	function warning($title, $message = '', $type = 'block'){
		echo '
		<div class="alert alert-'.$type.'">
			<a class="close" data-dismiss="alert">x</a>
			<h4 class="alert-heading">'.$title.'</h4>';
		if(!empty($message)){
			if(is_array($message)){
				echo '
			<ul>';
				foreach($message as $curmsg) echo '
				<li>'.$curmsg.'</li>';
				echo '
			</ul>';
			}
			else echo '
			<p>'.$message.'</p>';
		}
		echo '
		</div>';
	}
	function error($title, $message = ''){
		warning($title, $message, 'error');
	}
	function success($title, $message = ''){
		warning($title, $message, 'success');
	}
	function info($title, $message = ''){
		warning($title, $message, 'info');
	}
	
	# ===============================
	# Time ago
	# ===============================
	function timeago($time, $long = true){
		$dif = strtotime('now') - $time;
		if($dif < 1)
			return 'just now';
		if($dif > 60*60*24){
			$rtime = floor($dif / (60*60*24));
			$s = ($rtime == 1)?'':'s';
			return $rtime.(($long)?' day'.$s:'d').' ago';
		}
		if($dif > 60*60){
			$rtime = floor($dif / (60*60));
			$s = ($rtime == 1)?'':'s';
			return $rtime.(($long)?' hour'.$s:'h').' ago';
		}
		if($dif > 60){
			$rtime = floor($dif / 60);
			$s = ($rtime == 1)?'':'s';
			return $rtime.(($long)?' minute'.$s:'m').' ago';
		}
		$s = ($dif == 1)?'':'s';
		return $dif.(($long)?' second'.$s:'s').' ago';
	}
	
	# ===============================
	# Timers
	# ===============================
	function starttimer($name){
		$GLOBALS['timers'][$name] = strtotime('now') + microtime();
	}
	function timertime($name){
		return number_format((((strtotime('now') + microtime()) - $GLOBALS['timers'][$name]) * 1000), 0, '.', ',');
	}
	
	# ===============================
	# Redirect function
	# ===============================
	function redirect($url, $delay = 0){
		$url = (substr($url, 0, 4) == 'http')?$url:$GLOBALS['config']['baseurl'].$url;
		if((int)$delay == 0){
			if(headers_sent())
				echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
			else
				header('location: '.$url);
		}
		else
			echo '<meta http-equiv="refresh" content="'.$delay.';url='.$url.'" />';
	}
	
	# ===============================
	# SELF URL (for form actions)
	# ===============================
	function selfurl(){
		return 'http'.(empty($_SERVER['HTTPS'])?'':($_SERVER['HTTPS'] == 'on')?'s':'').'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
	}