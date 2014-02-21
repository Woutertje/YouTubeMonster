<?php

# Start global timer
$GLOBALS['timers']['main'] = strtotime('now') + microtime();

# Setup the database connection
require 'core/config.php';
require 'core/startup.php';

# Strip all ? shit from facebook
if (strpos(selfurl(), '?')) {
	header("Location: ".strstr(selfurl(), '?', true));
	return;
}
	
# Init account
require 'core/account.php';

# Set page globals
global $db, $curpage, $loggedin, $config;

# ===================================
# START TEMPLATE
# ===================================
$template = new template('main.html');

# ===================================
# Fill content
# ===================================
$page = empty($_GET['page']) ? 'home' : $_GET['page'];
switch ($page) {
	default:
		header('HTTP/1.0 404 Not Found');
		$page = new notFoundPage();
	break;
}
$template->setTag('page', $page->getContent());

# ===================================
# Important blocks
# ===================================
$template
	->parseFile('topnav', './core/menu.php')
	->parseFile('footer', './core/footer.php')
	->parseFile('loginbox', './pages/loginbox.php');

# ===================================
# Global repalcements
# ===================================
$template
	->setTagLoop('jsfiles', $GLOBALS['jsfiles'])
	->setTagLoop('cssfiles', $GLOBALS['cssfiles'])
	->setTag('pageimage', (empty($GLOBALS['pageimage'])) ? '{base}{templateroot}/images/logo.png' : $GLOBALS['pageimage'])
	->setTag('domain', $_SERVER['SERVER_NAME'])
	->setTag('facebookappid', $GLOBALS['fbappid'])
	->setTag('metacontent', $GLOBALS['seo_description'])
	->setTag('templateroot', $config['root'] . 'templates/' . $GLOBALS['config']['template'], '', false)
	->setTag('curpage', $page->getFixedTitle())
	->setTag('pagetitle', $page->getTitle())
	->setTag('base', $GLOBALS['config']['baseurl'])
	->setTag('year', date('Y'));

# ===================================
# Finish
# ===================================
$template->setTag('generationTime', timertime('main'));
echo $template->getContent();

# Close the database connection
require 'core/finish.php';