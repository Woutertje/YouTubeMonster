<?php
	# File globals
	global $loggedin;

	starttimer('footer');
	starttimer('recentlyadded');
	echo '
				<div class="box">
					<h4>Recently added</h4>';
	$latest = json_decode(file_get_contents('cache/latest.json'), true);
	$count = 0;
	foreach($latest as $latestvid){
		$count++;
		if($count > 5)
			break;
		echo '
					<span>&#9679;</span> <a href="./c/'.$latestvid['categoryurl'].'/'.$latestvid['urltitle'].'" title="In '.$latestvid['category'].', '.timeago($latestvid['addedon']).'">'.substr($latestvid['title'], 0, 35).'...</a><br />';
	}
	timertime('recentlyadded');
	starttimer('bestrated');
	echo '
				</div>
				<div class="box">
					<h4>Best rated today</h4>';
	$latest = json_decode(file_get_contents('cache/rates.json'), true);
	$count = 0;
	foreach($latest as $latestvid){
		$count++;
		if($count > 5)
			break;
		echo '
					<span>&#9679;</span> <a href="./c/'.$latestvid['categoryurl'].'/'.$latestvid['urltitle'].'" title="In '.$latestvid['category'].'">'.substr($latestvid['title'], 0, 25).'...</a> +'.number_format($latestvid['scoretoday'], 0, '', '.').'<br />';
	}
	if($count == 0)
		echo '
					<span>&#9679;</span> No video\'s rated today.';
	timertime('bestrated');
	echo '
				</div>
				<div class="box">
					<h4>Important pages</h4>
					<span>&#9679;</span> <a href="./">Homepage</a><br />
					<span>&#9679;</span> <a href="./contact">Contact</a><br />
					<span>&#9679;</span> <a href="'.(($loggedin)?'./suggest-video':'javascript:loginbox(\'suggest-video\')').'">Suggest new video</a><br />
					<span>&#9679;</span> <a href="'.(($loggedin)?'./suggest-channel':'javascript:loginbox(\'suggest-channel\')').'">Suggest new channel</a><br />
					<span>&#9679;</span> <a href="'.(($loggedin)?'./profile':'javascript:loginbox(\'profile\')').'">Your profile</a>
				</div>
				<div class="box">
					<h4>Share YouTubeMonster</h4>
					<div style="padding-bottom: 6px;"><fb:like href="https://www.facebook.com/pages/Youtubemonster/215125995328510" send="false" width="250" show_faces="false" font="" data-layout="button_count"></fb:like></div>
					<div><a href="https://twitter.com/share" class="twitter-share-button" data-url="http://youtubemonster.com/" data-via="YT_Monster">Tweet</a></div>
					<h4>Open Source on GitHub</h4>
					<iframe src="http://ghbtns.com/github-btn.html?user=FuturePortal&repo=YouTubeMonster&type=watch" allowtransparency="true" frameborder="0" scrolling="0" width="62" height="20"></iframe>
				</div>';
	timertime('footer');