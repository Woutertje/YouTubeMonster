<?php
	# Page globals
	global $loggedin;
	
	# You must be logged in to see your profile
	if(!$loggedin)
		redirect();
		
	# Switch profile action
	switch($_GET['sub2']){
		# Default overview
		default:
			require './pages/profile/overview.php';
		break;
	}

	/*global $db;
	
	# You must be logged in to see this page
	if(!$GLOBALS['loggedin']){
		redirect();
		return;
	}
	
	# Grab current list if set
	if((int)$_GET['sub2'] > 0){
		$curlist = $db->query('
			SELECT
				`playlists`.*,
				(
					SELECT COUNT(*)
					FROM `playlist_channel`
					WHERE `playlist_channel`.`playlist_id` = `playlists`.`id`
				) AS `linkedchannels`
			FROM `playlists`
			WHERE `playlists`.`id` = ?
			AND `playlists`.`user_id` = ?
			LIMIT 1
		', array(
			$_GET['sub2'],
			$GLOBALS['curuser']['id']
		))->fetch();
		# If list doesn't exists
		if($curlist == null){
			redirect($_GET['page'].'/');
			return;
		}
	}

	switch($_GET['sub1']){
		# =============================
		# New/edit playlist
		# =============================
		case 'editlist': case 'newlist':
			# Set playlist title stuff
			if(isset($_POST['save'])){
				$listtitle = $_POST['title'];
				if(empty($listtitle))
					$listtitle = 'Custom playlist '.$curlist['id'];
				
				# Fix up title
				$listurltitle = substr(str_replace(array('---', '--'), '-', strtolower(preg_replace('/[^a-zA-Z0-9\-]/', '', str_replace(' ', '-', $_POST['urltitle'])))), 0, 35);
				$i = 1;
				if($listurltitle[strlen($listurltitle) - 1] == '-')
					$listurltitle = substr($listurltitle, 0, -1);
				if($listurltitle == '')
					$listurltitle = 'custom-list-'.$curlist['id'];
				$finishedtitle = $listurltitle;
				while($GLOBALS['db']->query('
					SELECT *
					FROM `playlists`
					WHERE `urltitle` = ?
					AND `id` != '.(int)$curlist['id'].'
				', array(
					$finishedtitle
				))->rowCount() > 0){
					$i++;
					$finishedtitle = $listurltitle.'-'.$i;
				}
			}
			switch($_GET['sub1']){
				# =============================
				# Edit
				# =============================
				case 'editlist':
					# Set post vals
					if(empty($_POST['title'])) $_POST['title'] = $curlist['title'];
					if(empty($_POST['urltitle'])) $_POST['urltitle'] = $curlist['urltitle'];
					# Set select channel query
					$channelquery = '
						SELECT
							`channels`.`id` AS `channelid`,
							`channels`.`category_id`,
							`channels`.`username` AS `channelname`,
							(
								SELECT COUNT(*)
								FROM `playlist_channel`
								WHERE `playlist_channel`.`playlist_id` = '.$curlist['id'].'
								AND `playlist_channel`.`channel_id` = `channels`.`id`
							) AS `isselected`
						FROM `channels`
					';
					# SAVE CHANGES
					if(isset($_POST['save'])){
						$db->update('playlists', array(
							'title' => $listtitle,
							'urltitle' => $finishedtitle
						), array(
							'id' => $curlist['id']
						));
						$db->delete('playlist_channel', array(
							'playlist_id' => $curlist['id']
						));
					}
				break;
				# =============================
				# New
				# =============================
				case 'newlist':
					# Set select channel query
					$channelquery = '
						SELECT
							`id` AS `channelid`,
							`category_id`,
							`username` as `channelname`
						FROM `channels`
					';
					# SAVE CHANGES
					if(isset($_POST['save'])){
						$db->insert('playlists', array(
							'title' => $listtitle,
							'urltitle' => $finishedtitle,
							'user_id' => $GLOBALS['curuser']['id']
						));
						$curlist['id'] = $db->lastinsertid();
					}
				break;
			}
			if(isset($_POST['save'])){
				foreach($_POST['channel'] as $id => $crap)
					$db->insert('playlist_channel', array(
						'channel_id' => $id,
						'playlist_id' => $curlist['id']
					));
				redirect($_GET['page'].'/');
				return;
			}
			# =============================
			# Editor
			# =============================
			$channels = array();
			$count = 0;
			foreach($db->query($channelquery) as $curchannel){
				$channels[$curchannel['category_id']][$count] = $curchannel;
				if($_GET['sub1'] == 'editlist'){
					if(isset($_POST['save']))
						$channels[$curchannel['category_id']][$count]['channelchecked'] = (isset($_POST['channel'][$curchannel['channelid']])?' checked="checked" ':'');
					else
						$channels[$curchannel['category_id']][$count]['channelchecked'] = ($curchannel['isselected'] > 0)?' checked="checked" ':'';
				}
				else{
					$channels[$curchannel['category_id']][$count]['channelchecked'] = isset($_POST['channel'][$curchannel['channelid']])?' checked="checked" ':'';
				}
				$count++;
			}
			
			$categories = $db->query('
				SELECT
					`id` AS `categoryid`,
					`title` AS `categoryname`
				FROM `categories`
				WHERE (
					SELECT COUNT(*)
					FROM `channels` AS `two`
					WHERE `two`.`category_id` = `categories`.`id`
				) != 0
				ORDER BY `title`
			')->fetchAll();
			foreach($categories as $currow => $curcat)
				$categories[$currow]['channels'] = $channels[$curcat['categoryid']];
			$page = new template('editlist.html');
			$page->setcontent('title', $_POST['title']);
			$page->setcontent('urltitle', $_POST['urltitle']);
			$page->repeater('categories', $categories);
			echo $page->display();
		break;
		# =============================
		# Default yourprofile view
		# =============================
		case 'removelist':
			if(isset($_POST['confirmremove'])){
				$db->delete('playlists', array(
					'id' => $curlist['id']
				));
				redirect('./profile/');
				return;
			}
			$page = new template('removelist.html');
			$page->setcontent('listtitle', $curlist['title']);
			$page->setcontent('channelcount', $curlist['linkedchannels']);
			echo $page->display();
		break;
		# =============================
		# Default yourprofile view
		# =============================
		default:
			$page = new template('profile.html');
			$page->setcontent('name', $GLOBALS['curuser']['name']);
			$page->repeater('playlists', $db->query('
				SELECT
					`playlists`.*,
					(
						SELECT COUNT(*)
						FROM `playlist_channel`
						WHERE `playlist_channel`.`playlist_id` = `playlists`.`id`
					) AS `linkedchannels`
				FROM `playlists`
				WHERE `playlists`.`user_id` = ?
			', array(
				$GLOBALS['curuser']['id']
			))->fetchAll());
			echo $page->display();
		break;
	}*/