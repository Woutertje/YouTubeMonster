<?php
	global $cats, $loggedin, $curuser;
	
	starttimer('menu');

	# Display list
	echo '
				<ul>
					<li><a href="./">Home</a></li>
					<li><a href="./music">Music player</a></li>
					<li><a href="./categories">Categories</a></li>
					<li><a href="./search">Search</a></li>
					<li><a href="'.(($loggedin)?'./suggest-video':'javascript:loginbox(\'suggest-video\')').'">Suggest new video</a></li>
					<li><a href="'.(($loggedin)?'./suggest-channel':'javascript:loginbox(\'suggest-channel\')').'">Suggest new channel</a></li>
					<li><a href="./contact">Contact</a></li>
				</ul>';
	
	# Unset categorylist
	unset($GLOBALS['cats']);
	
	timertime('menu');
	
	/*
	
					<li><span>Profile</span>
						<ul>
							<li><a href="'.(($loggedin)?'./profile':'javascript:loginbox(\'profile\')').'">'.(($loggedin)?'Profile: '.$curuser['username']:'Your profile').'</a></li>
							<li><a href="'.(($loggedin)?'./profile/lists':'javascript:loginbox(\'lists\')').'">Your channel list streams</a></li>
							<li><a href="'.(($loggedin)?'./profile/favorites':'javascript:loginbox(\'favorites\')').'">Favorite video\'s</a></li>
							'.(($loggedin)?'<li><a href="./logout">Log out</a></li>':'').'
						</ul>
					</li>
	*/