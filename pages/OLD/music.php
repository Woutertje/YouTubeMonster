<?php
	# Page globals
	global $db, $config, $playtype;

	starttimer('musicplayer');
	
	if(empty($_GET['sub1'])){
		if(isset($_POST['play'])){
			$playing = array();
			if($_POST['playtype'] == 'random')
				$playing[] = 'r';
			foreach($_POST['play'] as $key => $value)
				$playing[] = $key;
			header('location: '.$config['baseurl'].'music/'.implode('-', $playing));
		}
		addjs('cycle2.js');
		echo '
		<div class="seperator"></div>
		<div class="wrap">
			<div class="textbar">
				<h1>Select styles to play</h1>
				<form action="'.selfurl().'" method="post">
					<div class="musicplayer">';
		foreach($db->query('
			SELECT `title`, `musictitle`, `lastvids`
			FROM `categories`
			WHERE `parent` = 4
		') as $cat){
			echo '
						<label>
							<input type="checkbox" style="display: none;" name="play['.$cat['musictitle'].']" />';
			if(!empty( $cat['lastvids'])){
				echo '
							<div class="cycle" data-cycle-slides="> div" data-cycle-paused="true">';
				foreach(explode(',', $cat['lastvids']) as $lastvid)
					echo '
								<div class="image" style="background-image: url(\'http://img.youtube.com/vi/'.$lastvid.'/0.jpg\');"></div>';
				echo '
							</div>';
			}
			echo '
							<div class="boxcontent">
								'.$cat['title'].'
							</div>
						</label>';
		}
		?>
					</div>
					<div class="clear"></div>
					<p>
						<label><input type="radio" name="playtype" value="newtoold" checked="checked" /> Latest songs</label>
						<label><input type="radio" name="playtype" value="random" /> Random</label>
					</p>
					<p>
						<input type="submit" value="Play!" name="playsongs" />
					</p>
				</form>
			</div>
		</div>
		<script type="text/javascript">
			$('.cycle').cycle({
				speed: 700,
				manualSpeed: 700,
				timeout: 1000
			});
			$('label').hover(
				function(){
					$(this).children('.cycle').cycle('resume');
				},
				function(){
					$(this).children('.cycle').cycle('pause');
				}
			);
			$('label').click(function(){
				$(this).css('opacity', $(this).find('input').is(':checked')?1:0.5);
			});
		</script>
		<div class="seperator"></div>
		<?php
		return;
	}
	
	# =================================
	# Load current video
	# =================================
	function loadfirst(){
		# Global for this func
		global $db;
	
		# Grab categories from db
		$categories = array();
		foreach($db->query('
			SELECT `id`, `musictitle`
			FROM `categories`
			WHERE `parent` = 4
		') as $cat)
			$categories[$cat['musictitle']] = $cat;
		$urlcats = explode('-', $_GET['sub1']);
		$catids = array();
		$random = false;
		# Match url categories with database cats
		foreach($urlcats as $cat)
			if($cat == 'r')
				$random = true;
			else if((int)$categories[$cat]['id'] > 0)
				$catids[] = '`category_id` = '.$categories[$cat]['id'];
		# When no valid cats leave
		if(count($catids) == 0)
			redirect($_GET['page']);
		# Load first video
		$GLOBALS['curvid'] = $db->query('
			SELECT *
			FROM `videos`
			WHERE (
				'.implode('
				OR ', $catids).'
			)
			AND `status` != 2
			ORDER BY '.(($random)?'RAND()':'`addedon` DESC').'
			LIMIT 1
		')->fetch();
		# Move to first video
		redirect('music/'.$_GET['sub1'].'/'.$GLOBALS['curvid']['urltitle']);
	}
	
	if(empty($_GET['sub2'])){
		loadfirst();
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
		
		if(substr($_GET['sub1'], 0, 2) == 'r-')
			$playtype = 'random';
			
		timertime('currentvid');
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
	$GLOBALS['curpage']['id'] = 0;
	
	require './pages/player.php';
	
	timertime('musicplayer');