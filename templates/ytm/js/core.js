$(function(){
	// ==============================
	// Menu
	// ==============================
	var menuopen = false;
	function openmenu(){
		$('header .menubtn').html('<i class="fa fa-times"></i>');
		$('#menu').stop().animate({
			right: '0px'
		}, 500);
		/*$('#wrapper').stop().animate({
			right: '350px'
		}, 500);*/
		$('#menufade').css('display', 'block').stop().animate({
			right: '350px',
			opacity: 1
		}, 500);
		menuopen = true;
	}
	function closemenu(){
		$('header .menubtn').html('<i class="fa fa-bars"></i>');
		$('#menu').stop().animate({
			right: '-353px'
		}, 500);
		/*$('#wrapper').stop().animate({
			right: '0px'
		}, 500);*/
		$('#menufade').stop().animate({
			right: '0px',
			opacity: 0
		}, 500, function(){
			$(this).css('display', 'none');
		});
		menuopen = false;
	}
	$('header .menubar').click(function(){
		if(menuopen)
			closemenu();
		else
			openmenu();
	});
	$('#menufade').click(function(){
		closemenu();
	});
});

function createCookie(name, value, days){
	if (days) {
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	}
	else var expires = "";
	document.cookie = name+"="+value+expires+"; path=/";
}

function readCookie(name) {
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for(var i=0;i < ca.length;i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1,c.length);
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
	}
	return null;
}


// ==============================
// Social media platforms
// ==============================

// Facebook
$(document).ready(function() {
	$.getScript('//connect.facebook.net/en_UK/all.js', function(){
		FB.init({
			appId: '475476499162745',
			xfbml: true
		});     
		//$('#loginbutton,#feedbutton').removeAttr('disabled');
		//FB.getLoginStatus(updateStatusCallback);
	});
});

// Twitter
!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");