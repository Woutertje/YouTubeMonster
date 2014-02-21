<?php
	global $db;
	
	starttimer('homepage');
	addjs('nivoslider.js');
	addcss('nivoslider.css');
	
?>
		<div class="slider">
			<div id="slider" class="nivoSlider">
				<a href="./c/humor"><img src="./{templateroot}/images/homeslider/humor.png" data-thumb="images/up.jpg" alt="" /></a>
				<a href="./c/dubstep"><img src="./{templateroot}/images/homeslider/dubstep.png" data-thumb="images/up.jpg" alt="" /></a>
				<a href="./c/cars"><img src="./{templateroot}/images/homeslider/cars.png" data-thumb="images/up.jpg" alt="" /></a>
			</div>
		</div>
		<div class="seperator"></div>
		<script type="text/javascript">
			$(window).load(function() {
				$('#slider').nivoSlider({
					//animSpeed: 500,
					//pauseTime: 5000,
					directionNav: false,
					controlNav: false,
					pauseTime: 5000,
					randomStart: true
				});
			});
		</script>
		<div class="wrap">
			<div id="grid">
<?php
	
	$latest = json_decode(file_get_contents('cache/latest.json'), true);
	
	foreach($latest as $latestvid){
		echo '
					<div class="item">
						<a href="./c/'.$latestvid['categoryurl'].'/'.$latestvid['urltitle'].'" class="thumb" style="background-image: url(\'http://img.youtube.com/vi/'.$latestvid['videotag'].'/0.jpg\');"></a>
						<div class="txt">
							<p>NEW: <a href="./c/'.$latestvid['categoryurl'].'/'.$latestvid['urltitle'].'">'.$latestvid['title'].'</a>, '.timeago($latestvid['addedon']).' in <a href="./c/'.$latestvid['categoryurl'].'">'.$latestvid['category'].'</a>.</p>
						</div>
					</div>';
	}
?>
			</div>
		</div>
		<script type="text/javascript">
		var container = document.querySelector('#grid');
		var msnry = new Masonry( container, {
			// options
			gutter: 20,
			columnWidth: 285,
			itemSelector: '.item',
			isFitWidth: true,
			transitionDuration: 0
		});
		</script>
		<div class="seperator"></div><?php timertime('homepage'); ?>