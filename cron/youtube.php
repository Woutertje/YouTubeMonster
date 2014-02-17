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
	
	debug('Checking if a channel requires updating...');
	
	$curchannel = $GLOBALS['db']->query('
		SELECT *
		FROM `channels`
		WHERE `lastupdate` < ?
		ORDER BY `lastupdate`
		LIMIT 1
	', array(
		strtotime('now') - 1800
	))->fetch();
	if($curchannel == null)
		die('No channel requires updating.');
		
	debug('Update required for channel "'.$curchannel['username'].'".');
	
	# ========================
	# Set channel as updated
	# ========================
	$GLOBALS['db']->update('channels', array(
		'lastupdate' => strtotime('now')
	), array(
		'id' => $curchannel['id']
	));
	
	# ========================
	# GRABBING FEED
	# ========================
	function between($betw, $betw2, $str){
		$str = " " . $str;
		$pos = strpos($str, $betw);
		if($pos > 0){
			$str2 = substr($str, $pos + strlen($betw));
			$pos2 = strpos($str2, $betw2);
			if ($pos2 == 0) return '';
			return substr($str2, 0, $pos2);
		}
		else return '';
	}
	
	$youtuberss = simplexml_load_file('http://gdata.youtube.com/feeds/api/users/'.$curchannel['username'].'/uploads?orderby=updated');
	
	$newvid = array();
	
	$count = 1;
	foreach($youtuberss->entry as $entry){
		//echo '<pre>'.print_r($entry, true).'</pre>';
		$count++;
		$vid = between('?v=', '&', print_r($entry->link, true));
		$title = $entry->title;
		debug('<b>VIDEO</b> ['.$vid.'] '.$title);
		
		if($GLOBALS['db']->query('
			SELECT `id`
			FROM `videos`
			WHERE `tag` = ?
		', array(
			$vid
		))->rowCount() > 0)
			debug('<span style="color: #D00000;">Video is already on the website.</span>');
		else{
			debug('<span style="color: #00D000;">This video is not on the website yet.</span>');
			$newvid = array(
				'title' => (string)$title,
				//'content' => (string)$entry->content,
				'tag' => $vid
			);
		}
		
		if($count > $check_amount_of_new_vids) break;
	}
	
	if(empty($newvid))
		die('<br /><span style="color: #D00000;">No new videos ('.number_format((((strtotime('now') + microtime()) - $t) * 1000), 2, '.', '').' ms).</span>');
	
	# ========================
	# Grab video details
	# ========================
	$details = json_decode(str_replace('$', '_', file_get_contents('http://gdata.youtube.com/feeds/api/videos/'.$newvid['tag'].'?v=2&prettyprint=true&alt=json')));
	$description = $details->entry->media_group->media_description->_t;
	$newvid['content'] = (string)$description;
	
	
	# ========================
	# INSERTING NEW VID IF
	# ========================
	debug('<br /><b>ADDING</b> ['.$newvid['tag'].'] '.$newvid['title']);
	
	$newtitle = substr(str_replace(array('---', '--'), '-', strtolower(preg_replace('/[^a-zA-Z0-9\-]/', '', str_replace(' ', '-', $newvid['title'])))), 0, 35);
	$i = 1;
	if($newtitle[strlen($newtitle) - 1] == '-')
		$newtitle = substr($newtitle, 0, -1);
	if($newtitle == '') $newtitle = strtolower($curchannel['username']);
	$newvid['urltitle'] = $newtitle;
	while($GLOBALS['db']->query('
		SELECT *
		FROM `videos`
		WHERE `urltitle` = ?
	', array(
		$newvid['urltitle']
	))->rowCount() > 0){
		$i++;
		$newvid['urltitle'] = $newtitle.'-'.$i;
		if($i > 50) die('FAIL SAFE 50 TIMES NAME IN USE. TERMINATE.');
	}
	debug('URL title: '.$newvid['urltitle']);
	$newvid['channel_name'] = $curchannel['username'];
	$newvid['addedon'] = strtotime('now');
	$newvid['channel_id'] = $curchannel['id'];
	$newvid['category_id'] = $curchannel['category_id'];
	
	$GLOBALS['db']->insert('videos', $newvid);
	
	# ========================
	# Update newst vids json
	# ========================
	$newest = array();
	$cats = array();
	foreach($db->query('
		SELECT
			`category_id`,
			`title`,
			`urltitle`,
			`tag`,
			`addedon`
		FROM `videos`
		ORDER BY `addedon` DESC
		LIMIT 16
	') as $curvideo){
		if(empty($cats[$curvideo['category_id']]))
			$cats[$curvideo['category_id']] = $db->query('
				SELECT
					`title`,
					`urltitle`
				FROM `categories`
				WHERE `id` = ?
			', array(
				$curvideo['category_id']
			))->fetch();
		$newest[] = array(
			'videotag' => $curvideo['tag'],
			'title' => $curvideo['title'],
			'category' => $cats[$curvideo['category_id']]['title'],
			'categoryurl' => $cats[$curvideo['category_id']]['urltitle'],
			'urltitle' => $curvideo['urltitle'],
			'addedon' => $curvideo['addedon']
		);
	}
	file_put_contents('../cache/latest.json', json_encode($newest));
	
	# ========================
	# Update latest thumbnails
	# ========================
	$tags = array();
	foreach($db->query('
		SELECT
			`tag`
		FROM `videos`
		WHERE `category_id` = ?
		ORDER BY `addedon` DESC
		LIMIT 5
	', array(
		$curchannel['category_id']
	)) as $curvideo)
		$tags[] = $curvideo['tag'];
	$db->update('categories', array(
		'lastvids' => implode(',', $tags)
	), array(
		'id' => $curchannel['category_id']
	));
	
	# ========================
	# SPAM ON TWITTER
	# ========================
	/*include('../classes/OAuth.php');
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
	
	# Category
	$category = $GLOBALS['db']->query('
		SELECT `urltitle`
		FROM `categories`
		WHERE `id` = ?
	', array(
		$curchannel['category_id']
	))->fetch();
	$category = $category['urltitle'];
	
	# Spam on twitter
	try{
		$twitter->send($newvid['title'].': '.$twitter->shorturl('http://youtubemonster.com/c/'.$category.'/'.$newvid['urltitle'].'/').' #'.$curchannel['username'].' #'.str_replace('-', '_', ucfirst($category)));
		debug('Spammed on twitter!');
	}
	catch(TwitterException $e){
		debug('<span style="color: #D00000;">Did not post on twitter: '.$e->getMessage().'</span>');
	}*/
	
	# Ending.
	debug('');
	debug('Ended ('.number_format((((strtotime('now') + microtime()) - $t) * 1000), 2, '.', '').' ms).');