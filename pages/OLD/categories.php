		<div class="seperator"></div>
		<div class="wrap">
			<div class="textbar categories">
<?php
	global $loggedin, $curuser, $db;
	
	starttimer('categories');
	addjs('cycle2.js');
	
	# Grabbing all categories
	$cats = array();
	foreach($db->query('
		SELECT `categories`.*
		FROM `categories`
		WHERE (
			SELECT COUNT(*)
			FROM `channels` AS `two`
			WHERE `two`.`category_id` = `categories`.`id`
		) != 0
		ORDER BY `categories`.`title`
	') as $curcat)
		if($curcat['parent'] != 4)
			$cats[] = $curcat;
	
	$count = 0;
	foreach($cats as $cat){
?>
		<div class="box">
<?php
			$count++;
			$lastvids = explode(',', $cat['lastvids']);
			echo '
			<div class="cycle" data-cycle-slides="> a" data-cycle-paused="true">';
			foreach(explode(',', $cat['lastvids']) as $lastvid)
				echo '
				<a href="./c/'.$cat['urltitle'].'/" class="image" style="background-image: url(\'http://img.youtube.com/vi/'.$lastvid.'/0.jpg\');"></a>';
			echo '
			</div>
			<div class="seperator"></div>';
?>
			<div class="boxcontent">
				<a href="./c/<?=$cat['urltitle']?>/"><?=$cat['title']?></a>
			</div>
		</div>
<?php
	}
	
	timertime('categories');
?>
			</div>
			<div class="clear"></div>
		</div>
		<script type="text/javascript">
			$('.cycle').cycle({
				speed: 700,
				manualSpeed: 700,
				timeout: 1000
			});
			$('.box').hover(
				function(){
					$(this).children('.cycle').cycle('resume');
				},
				function(){
					$(this).children('.cycle').cycle('pause');
				}
			);
		</script>
		<div class="seperator"></div>