<?php
	session_destroy();
	setcookie('me', '', 0, '/');
	redirect();
	return;