<?php
	# Keep time
	$t = strtotime('now') + microtime();
	
	# Check if type and id are set correctly
	$type = $_POST['type'];
	$videoid = (int)$_POST['videoid'];
	if(($type != '-' && $type != '+') || $videoid == 0)
		die(json_encode(array(
			'score' => '[error]',
			'scoretoday' => '[error]',
			'votes' => '[error]',
			'took' => number_format((((strtotime('now') + microtime()) - $t) * 1000), 2, '.', '').'ms'
		)));
		
	# setup
	include('../core/config.php');
	$GLOBALS['config']['classesroot'] = '../classes/';
	include('../core/startup.php');

	# Set globals
	global $db;

	# Grab current video
	$video = $db->query('
		SELECT `id`, `score`, `scoretoday`, `votes`
		FROM `videos`
		WHERE `id` = ?
	', array(
		$videoid
	))->fetch();
	if($video == null)
		die(json_encode(array(
			'score' => '[error]',
			'scoretoday' => '[error]',
			'votes' => '[error]',
			'took' => number_format((((strtotime('now') + microtime()) - $t) * 1000), 2, '.', '').'ms'
		)));
		
	# Check if voted already
	$slug = $_SERVER['REMOTE_ADDR'].$video['id'];
	if($db->query('
		SELECT COUNT(*)
		FROM `rates`
		WHERE `slug` = ?
	', array(
		$slug
	))->fetchColumn() > 0){
		# Do nothing.
	}
	else{
		# Update score
		$video['votes']++;
		if($type == '+'){
			$video['score']++;
			$video['scoretoday']++;
		}
		else{
			$video['score']--;
			$video['scoretoday']--;
		}
		
		# Insert vote into db
		$db->insert('rates', array(
			'slug' => $slug,
			'video_id' => $video['id'],
			'type' => $type
		));
		# Update video rating
		$db->query('
			UPDATE `videos`
			SET
				`score` = `score` '.$type.' 1,
				`scoretoday` = `scoretoday` '.$type.' 1,
				`votes` = `votes` + 1
			WHERE `id` = ?
		', array(
			$video['id']
		));
		
		# Build ratings cache json
		$rates = array();
		foreach($db->query('
			SELECT
				`category_id`,
				`title`,
				`urltitle`,
				`tag`,
				`scoretoday`
			FROM `videos`
			WHERE `scoretoday` > 0
			ORDER BY `scoretoday` DESC
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
			$rates[] = array(
				'videotag' => $curvideo['tag'],
				'title' => $curvideo['title'],
				'category' => $cats[$curvideo['category_id']]['title'],
				'categoryurl' => $cats[$curvideo['category_id']]['urltitle'],
				'urltitle' => $curvideo['urltitle'],
				'scoretoday' => $curvideo['scoretoday']
			);
		}
		file_put_contents('../cache/rates.json', json_encode($rates));
	}
	
	# Return result
	echo json_encode(array(
		'score' => (($video['score'] > 0)?'+':'').number_format($video['score'], 0, '', '.'),
		'scoretoday' => (($video['scoretoday'] > 0)?'+':'').number_format($video['scoretoday'], 0, '', '.'),
		'votes' => number_format($video['votes'], 0, '', '.'),
		'took' => number_format((((strtotime('now') + microtime()) - $t) * 1000), 2, '.', '').'ms'
	));