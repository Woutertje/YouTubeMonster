<?php
	# Setup the database connection
	include('config.php');
	$GLOBALS['config']['templateroot'] = '../templates/';
	$GLOBALS['config']['classesroot'] = '../classes/';
	include('startup.php');
	
	global $db;
	
	# Set XML header and open document
	echo '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

<!-- Pages -->
';

	foreach($db->query('
		SELECT
			`urltitle`,
			`googlepriority`,
			`lastupdate`
		FROM `pages`
		WHERE `insitemap` = 1
	') as $curpage)
	echo '
	<url>
		<loc>'.$GLOBALS['config']['baseurl'].(($curpage['urltitle'] == 'home')?'':$curpage['urltitle']).'</loc>
		<lastmod>'.date('c', $curpage['lastupdate']).'</lastmod>
		<changefreq>weekly</changefreq>
		<priority>'.$curpage['googlepriority'].'</priority>
	</url>';

echo '

<!-- Last 5000 video\'s -->
';

	$caturls = array();
	foreach($db->query('
		SELECT 
			`id`,
			`urltitle`
		FROM `categories`
	') as $curcat)
		$caturls[$curcat['id']] = $curcat['urltitle'];
		
	# Looping trough all pages for this site
	foreach($db->query('
		SELECT 
			`category_id`,
			`urltitle`,
			`addedon`
		FROM `videos`
		WHERE `status` != 2
		ORDER BY `addedon` DESC
		LIMIT 5000
	') as $curvid){
		echo '
	<url>
		<loc>'.$GLOBALS['config']['baseurl'].'c/'.$caturls[$curvid['category_id']].'/'.$curvid['urltitle'].'</loc>
		<lastmod>'.date('c', $curvid['addedon']).'</lastmod>
		<changefreq>monthly</changefreq>
		<priority>0.3</priority>
	</url>';
	}
	
	# Closing XML
	echo '
	
</urlset>';