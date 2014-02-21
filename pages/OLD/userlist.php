<?php
	starttimer('userlist');

	$GLOBALS['curlist'] = $GLOBALS['db']->query('
		SELECT *
		FROM `playlists`
		WHERE `urltitle` = ?
	', array(
		$_GET['sub1']
	))->fetch();
	if($GLOBALS['curlist'] == null){
		redirect();
		return;
	}
	$gotvid = false;
	
	# =================================
	# Load all linked channels in this
	# playlist
	# =================================
	function loadchannellist(){
		$GLOBALS['channellist'] = array();
		foreach($GLOBALS['db']->query('
			SELECT
				`channel_id`
			FROM `playlist_channel`
			WHERE `playlist_id` = ?
		', array(
			$GLOBALS['curlist']['id']
		)) as $curchannel)
			$GLOBALS['channellist'][] = '`channel_id` = '.$curchannel['channel_id'];
		$GLOBALS['channellist'] = '('.implode(' OR ', $GLOBALS['channellist']).')';
	}
	
	# =================================
	# Load current video
	# =================================
	function loadfirst(){
		loadchannellist();
		# Load first video
		$GLOBALS['curvid'] = $GLOBALS['db']->query('
			SELECT *
			FROM `videos`
			WHERE '.$GLOBALS['channellist'].'
			AND `status` != 2
			ORDER BY `addedon` DESC
			LIMIT 1
		', array(
			$GLOBALS['curcat']['id']
		))->fetch();
	}
	
	if(empty($_GET['sub2'])){
		loadfirst();
		redirect('l/'.$_GET['sub1'].'/'.$GLOBALS['curvid']['urltitle']);
		return;
	}
	else{
		$GLOBALS['curvid'] = $GLOBALS['db']->query('
			SELECT *
			FROM `videos`
			WHERE `urltitle` = ?
		', array(
			$_GET['sub2']
		))->fetch();
		if($GLOBALS['curvid'] == null)
			loadfirst();
	}
	$GLOBALS['curpage']['video'] = $GLOBALS['curvid'];
	
	# =================================
	# Load current vid details
	# =================================
	$thiscat = $GLOBALS['db']->query('
		SELECT `urltitle`, `description`
		FROM `categories`
		WHERE `id` = ?
	', array(
		$GLOBALS['curvid']['category_id']
	))->fetch();
	
	$GLOBALS['curpage']['image'] = 'http://img.youtube.com/vi/'.$GLOBALS['curvid']['tag'].'/2.jpg';
	$GLOBALS['curpage']['metacontent'] = $thiscat['description'];
	$GLOBALS['curpage']['thisurl'] = 'c/'.$thiscat['urltitle'].'/'.$GLOBALS['curvid']['urltitle'];
	$GLOBALS['curpage']['id'] = $GLOBALS['curlist']['id'];
		
	timertime('userlist');
		
	require './pages/player.php';