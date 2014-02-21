<?php
	starttimer('categorypage');

	$GLOBALS['curcat'] = $GLOBALS['db']->query('
		SELECT *
		FROM `categories`
		WHERE `urltitle` = ?
	', array(
		$_GET['sub1']
	))->fetch();
	if($GLOBALS['curcat'] == null){
		redirect();
		return;
	}
	$gotvid = false;
	
	# =================================
	# Load current video
	# =================================
	function loadfirst(){
		starttimer('loadfirst');
		# Load first video
		$GLOBALS['curvid'] = $GLOBALS['db']->query('
			SELECT *
			FROM `videos`
			WHERE `category_id` = ?
			AND `status` != 2
			ORDER BY `addedon` DESC
			LIMIT 1
		', array(
			$GLOBALS['curcat']['id']
		))->fetch();
		timertime('loadfirst');
	}
	
	if(empty($_GET['sub2'])){
		loadfirst();
		redirect('c/'.$_GET['sub1'].'/'.$GLOBALS['curvid']['urltitle']);
		return;
	}
	else{
		starttimer('currentvid');
		$GLOBALS['curvid'] = $GLOBALS['db']->query('
			SELECT *
			FROM `videos`
			WHERE `urltitle` = ?
		', array(
			$_GET['sub2']
		))->fetch();
		if($GLOBALS['curvid'] == null)
			loadfirst();
		timertime('currentvid');
	}
	$GLOBALS['curpage']['video'] = $GLOBALS['curvid'];
	
	# =================================
	# Load current vid details
	# =================================
	$GLOBALS['curpage']['image'] = 'http://img.youtube.com/vi/'.$GLOBALS['curvid']['tag'].'/2.jpg';
	$GLOBALS['curpage']['metacontent'] = $GLOBALS['curcat']['description'];
	$GLOBALS['curpage']['thisurl'] = 'c/'.$GLOBALS['curcat']['urltitle'].'/'.$GLOBALS['curvid']['urltitle'];
	$GLOBALS['curpage']['id'] = $GLOBALS['curcat']['id'];
	
	require './pages/player.php';
	
	timertime('categorypage');