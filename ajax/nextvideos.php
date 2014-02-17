<?php
	# Keep time
	$t = strtotime('now') + microtime();
	
	# Set vars
	/*
		Type: C(ategory)/L(ist from user)/U(sername from youtube)/F(avorite list from user)
		ID: spesific required id (category_id, user_id, playlist_id)
		TypeURL: url-slug
		VideoURL: url-slug
		Modus: Random/NewToOld
		CurTime: timestamp
	*/
	$nextvideos = array();
	$types =      array('c', 'l', 'u', 'f', 'music');
	$type =       $_POST['type'];
	$id =         (int)$_POST['id'];
	$curtime =    $_POST['curtime'];
	$typeurl =    $_POST['typeurl'];
	$moduses =    array('random', 'newtoold');
	$modus =      (empty($_POST['modus']))?'newtoold':$_POST['modus'];
	
	# Die on invalid setup
	if(!in_array($type, $types))
		$error = 'Invalid playlist type "'.$type.'".';
	if($id == 0 && $type != 'music')
		$error = 'ID 0.';
	if(!in_array($modus, $moduses))
		$error = 'Playlist modus "'.$modus.'" not found.';
	if($error)
		die(json_encode(array(
			'error' => $error,
			'nextvideos' => $nextvideos,
			'took' => number_format((((strtotime('now') + microtime()) - $t) * 1000), 2, '.', '').'ms'
		)));
	
	# setup
	include('../core/config.php');
	$GLOBALS['config']['classesroot'] = '../classes/';
	include('../core/startup.php');

	# Set globals
	global $db;

	# Find next 10 videos
	switch($type){
		### CATEGORY ###
		case 'c':
			switch($modus){
				case 'random':
					$query = $db->query('
						SELECT
							`title`,
							`urltitle`,
							`tag`,
							`channel_name`,
							`addedon`
						FROM `videos`
						WHERE `category_id` = ?
						AND `status` != 2
						ORDER BY RAND()
						LIMIT 10
					', array(
						$id
					));
				break;
				default:
					$query = $db->query('
						SELECT
							`title`,
							`urltitle`,
							`tag`,
							`channel_name`,
							`addedon`
						FROM `videos`
						WHERE `category_id` = ?
						AND `addedon` < ?
						AND `status` != 2
						ORDER BY `addedon` DESC
						LIMIT 10
					', array(
						$id,
						$curtime
					));
				break;
			}
		break;
		### USER CHANNEL LIST ###
		case 'l':
			$channellist = array();
			foreach($db->query('
				SELECT
					`channel_id`
				FROM `playlist_channel`
				WHERE `playlist_id` = ?
			', array(
				$id
			)) as $curchannel)
				$channellist[] = '`channel_id` = '.$curchannel['channel_id'];
			$channellist = '('.implode(' OR ', $channellist).')';
			
			switch($modus){
				case 'random':
					$query = $GLOBALS['db']->query('
						SELECT 
							`title`,
							`urltitle`,
							`tag`,
							`channel_name`,
							`addedon`
						FROM `videos`
						WHERE '.$channellist.'
						AND `status` != 2
						ORDER BY RAND()
						LIMIT 10
					');
				break;
				default:
					$query = $GLOBALS['db']->query('
						SELECT 
							`title`,
							`urltitle`,
							`tag`,
							`channel_name`,
							`addedon`
						FROM `videos`
						WHERE '.$channellist.'
						AND `addedon` < ?
						AND `status` != 2
						ORDER BY `addedon` DESC
						LIMIT 10
					', array(
						$curtime
					));
				break;
			}
		break;
		### MUSIC PLAYER ###
		case 'music':
			# Grab categories from db
			$categories = array();
			foreach($db->query('
				SELECT `id`, `musictitle`
				FROM `categories`
				WHERE `parent` = 4
			') as $cat)
				$categories[$cat['musictitle']] = $cat;
			$urlcats = explode('-', $typeurl);
			$catids = array();
			$random = false;
			# Match url categories with database cats
			foreach($urlcats as $cat)
				if($cat == 'r')
					$random = true;
				else if((int)$categories[$cat]['id'] > 0)
					$catids[] = '`category_id` = '.$categories[$cat]['id'];
			
			switch($modus){
				case 'random':
					$query = $db->query('
						SELECT *
						FROM `videos`
						WHERE (
							'.implode('
							OR ', $catids).'
						)
						AND `status` != 2
						ORDER BY RAND()
						LIMIT 10
					');
				break;
				default:
					$query = $db->query('
						SELECT *
						FROM `videos`
						WHERE (
							'.implode('
							OR ', $catids).'
						)
						AND `status` != 2
						AND `addedon` < ?
						ORDER BY `addedon` DESC
						LIMIT 10
					', array(
						$curtime
					));
				break;
			}
		break;
		### YOUTUBE CHANNEL ###
		case 'u':
		break;
		### USER FAVORITE VIDEO LIST ###
		case 'f':
		break;
	}
	
	### Loop trough query result ###
	foreach($query as $curvid){
		$nextvideos[] = array(
			'tag' => $curvid['tag'],
			'title' => $curvid['title'],
			'url' => $GLOBALS['config']['baseurl'].$type.'/'.$typeurl.'/'.$curvid['urltitle'],
			'channel' => $curvid['channel'],
			'addedon' => $curvid['addedon']
		);
	}
	
	# Return video's
	echo json_encode(array(
		'error' => false,
		'nextvideos' => $nextvideos,
		'took' => number_format((((strtotime('now') + microtime()) - $t) * 1000), 2, '.', '').'ms'
	));