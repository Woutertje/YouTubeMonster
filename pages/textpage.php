<?php
	global $curpage;
	
	$page = new template('textpage.html');
	$page->setcontent('content', $curpage['content']);
	echo $page->display();