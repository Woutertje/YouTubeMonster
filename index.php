<?php
	# Start global timer
	$GLOBALS['timers']['main'] = strtotime('now') + microtime();
	
	# Setup the database connection
	require 'core/config.php';
	require 'core/startup.php';
	
	# Strip all ? shit from facebook
	if(strpos(selfurl(), '?')){
		header("Location: ".strstr(selfurl(), '?', true));
		return;
	}
	
	# Temporary /old/ redirect
	if(strpos(selfurl(), '/old/')){
		header("HTTP/1.1 301 Moved Permanently"); 
		header("Location: ".str_replace('/old/', '/', selfurl()));
		return;
	}
		
	# Init account
	require 'core/account.php';
	
	# Set page globals
	global $db, $curpage, $loggedin;
	
	# ===================================
	# START TEMPLATE
	# ===================================
	$main = new template('main.html');
	
	# ===================================
	# Fill content
	# ===================================
	$page = empty($_GET['page'])?'home':$_GET['page'];
	$selectpagequery = '
		SELECT
			`title`,
			`googlekeywords`,
			`googledescription`,
			`content`,
			`pagefile`
		FROM `pages`
		WHERE `urltitle` = ?
	';
	$curpage = $db->query($selectpagequery, array(
		$page
	))->fetch();
	if($curpage == null){
		header('HTTP/1.0 404 Not Found');
		$curpage = $db->query($selectpagequery, array(
			404
		))->fetch();
		if($curpage == null)
			die('<b>file</b> index: [ERROR] Page not found, also no page with `urltitle` 404 found.');
	}
	# First set globals so the page can override them
	$GLOBALS['seo_description'] = $curpage['googledescription'];
	$GLOBALS['seo_keywords'] = $curpage['googlekeywords'];
	$GLOBALS['pagetitle'] = $curpage['title'];
	# Get page content
	$main->setcontent('page', './pages/'.$curpage['pagefile'].'.php');
	
	# ===================================
	# Share URL
	# ===================================
	switch($page){
		case 'user': $url = $GLOBALS['config']['baseurl'].$_GET['page'].'/'.$_GET['sub1']; break;
		case 'l': case 'c': case 'music': $url = $GLOBALS['config']['baseurl'].$GLOBALS['curpage']['thisurl']; break;
		default: $url = $GLOBALS['config']['baseurl'].$_GET['page']; break;
	}
	$main->setcontent('shareurl', $url);
	
	# ===================================
	# Important blocks
	# ===================================
	$main->setcontent('topnav', './core/menu.php');
	$main->setcontent('footer', './core/footer.php');
	$main->setcontent('loginbox', ($loggedin)?'':'./pages/loginbox.php');
	
	# ===================================
	# Global repalcements
	# ===================================
	$main->repeater('jsfiles', $GLOBALS['jsfiles']);
	$main->repeater('cssfiles', $GLOBALS['cssfiles']);
	$main->setcontent('pageimage', (empty($GLOBALS['pageimage']))?'{base}{templateroot}/images/logo.png':$GLOBALS['pageimage']);
	$main->setcontent('base', $GLOBALS['config']['baseurl']);
	$main->setcontent('domain', $_SERVER['SERVER_NAME']);
	$main->setcontent('facebookappid', $GLOBALS['fbappid']);
	$main->setcontent('metacontent', $GLOBALS['seo_description']);
	$main->setcontent('templateroot', $GLOBALS['config']['templateroot'].$GLOBALS['config']['template'], '', false);
	$main->setcontent('curpage', (strlen($GLOBALS['pagetitle']) > 25)?substr($GLOBALS['pagetitle'], 0, 22).'...':$GLOBALS['pagetitle']);
	$main->setcontent('pagetitle', $GLOBALS['pagetitle'].' | '.$GLOBALS['domaintitle']);
	$main->setcontent('year', date('Y'));
	
	# ===================================
	# Finish
	# ===================================
	echo $main->display();
	
	# Close the database connection
	include('core/finish.php');
	
	echo timertime('main').'
	</body>
</html>';