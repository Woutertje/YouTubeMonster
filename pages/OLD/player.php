<?php
	# Page globals
	global $db, $loggedin;

	# Set page details
	$GLOBALS['seo_description'] = $GLOBALS['curpage']['metacontent'];
	$curvid = $GLOBALS['curpage']['video'];
	$GLOBALS['pagetitle'] = $curvid['title'];
	$GLOBALS['pageimage'] = $GLOBALS['curpage']['image'];
?>
		<!-- Player container -->
		<section id="videobox" class="wrap">
			<div class="rating"><?php starttimer('scoreblock'); ?>
				<div class="scorebox">
					<div class="votes">Today <span class="videoscoretoday"><?=($curvid['scoretoday'] > 0)?'+'.number_format($curvid['scoretoday'], 0, '', '.'):number_format($curvid['scoretoday'], 0, '', '.')?></div>
					<span class="score videoscore"><?=($curvid['score'] > 0)?'+'.number_format($curvid['score'], 0, '', '.'):number_format($curvid['score'], 0, '', '.')?></span>
					<div class="votes"><span class="videovotes"><?=number_format($curvid['votes'], 0, '', '.')?></span> vote<span class="votess"><?=($curvid['votes'] == 1)?'':'s'?></span></div>
				</div>
				<div class="voting">
				<?php
					$slug = $_SERVER['REMOTE_ADDR'].$curvid['id'];
					$vote = $db->query('
						SELECT `type`
						FROM `rates`
						WHERE `slug` = ?
					', array(
						$slug
					))->fetch();
					if($vote == null){
				?>
					<div class="voteup vote"><i class="fa fa-plus-circle"></i></i></div>
					<div class="votedown vote"><i class="fa fa-minus-circle"></i></div>
				<?php
					}
					else if($vote['type'] == '+'){
				?>
					<div class="upvoted vote"><i class="fa fa-plus-circle"></i></div>
				<?php	
					}
					else{
				?>
					<div class="downvoted vote"><i class="fa fa-minus-circle"></i></div>
				<?php
					}
				?>
				</div>
			</div>
			<div id="playerwrapper">
				<div id="player"></div>
				<div id="playerstatus">Loading...</div>
				<div id="fullscreenhitzone"></div>
				<div id="playercontrols">
					<div class="playbtn pbtn" id="playbtn">
						<i class="fa fa-play"></i>
						<div class="icon iconsmall">
							<i class="fa fa-play"></i>
						</div>
					</div>
					<div class="pausebtn pbtn" id="pausebtn">
						<i class="fa fa-pause"></i>
						<div class="icon iconsmall">
							<i class="fa fa-pause"></i>
						</div>
					</div>
					<div class="progressbar">
						<div class="progress"></div>
						<div class="stime" id="playerstime"></div>
						<div class="etime" id="playeretime"></div>
						<div class="clicktimearea" id="clicktimearea"></div>
					</div>
					<div class="rightcontrols">
						<div class="pbtn">
							<i class="fa fa-heart"></i>
							<div class="hoverbox favoptions">
								<h3>List options</h3>
							</div>
							<div class="icon">
								<i class="fa fa-heart"></i>
							</div>
						</div>
						<div class="pbtn">
							<i class="fa fa-share"></i>
							<div class="hoverbox share">
								<div class="addthis_toolbox addthis_default_style addthis_32x32_style">
								<a class="addthis_button_facebook"></a>
								<a class="addthis_button_twitter"></a>
								<a class="addthis_button_pinterest_share"></a>
								<a class="addthis_button_google_plusone_share"></a>
								<a class="addthis_button_compact" title="More share options"></a><a class="addthis_counter addthis_bubble_style"></a>
								</div>
								<script type="text/javascript">
									var addthis_config = {
										data_track_addressbar: false,
										data_track_clickback: false
									};
									var addthis_share = {
										url: '{shareurl}'
									};
								</script>
								<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-525291ff77c41099"></script>
							</div>
							<div class="icon">
								<i class="fa fa-share"></i>
							</div>
						</div>
						<div class="pbtn">
							<span class="volicon"><i class="fa fa-volume-up"></i></span>
							<div class="hoverbox volume">
								<div class="soundbar">
									<div class="sound"></div>
								</div>
								<div class="clicksoundarea" id="clicksoundarea"></div>
							</div>
							<div class="icon volicon">
								<i class="fa fa-volume-up"></i>
							</div>
						</div>
						<div class="pbtn" id="fullscreen">
							<i class="fa fa-arrows-alt"></i>
							<div class="icon iconsmall">
								<i class="fa fa-arrows-alt"></i>
							</div>
						</div>
						<div class="pbtn">
							<i class="fa fa-arrow-right"></i>
							<div class="hoverbox nextvideo">
								<h3>Up next</h3>
								<div id="nextvideobox">
								</div>
							</div>
							<div class="icon">
								<i class="fa fa-arrow-right"></i>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
		
		
		<?php /*
		<div id="videotools">
			<div class="uptovid" onclick="uptovid()"></div>
			<div class="wrap">
				<script type="text/javascript">
					function addfav(){
						alert('Sorry, this feature is still under construction.');
					}
					function reportwrong(){
						alert('Sorry, this feature is still under construction.');
					}
					function videoiscorrect(){
						alert('Sorry, this feature is still under construction.');
					}
					function addtolist(){
						alert('Sorry, this feature is still under construction.');
					}
					function dontshow(){
						alert('Sorry, this feature is still under construction.');
					}
				</script>
				<div class="box">
					<h4>This video</h4>
					<?php
						if($curvid['status'] == 1):
					?>
					<p>
						<a href="<?=(($loggedin)?'javascript:addfav()':'javascript:loginbox()')?>" class="favvideo" title="Add to your favorite video's" style="border-radius: 5px;">&hearts; Add to your favorites</a>
					</p>
					<?php
						elseif($reports = $db->query('
							SELECT COUNT(*)
							FROM `video_validate`
							WHERE `video_id` = ?
						', array(
							$curvid['id']
						))->fetchColumn() > 0):
					?>
					<p>
						<a href="<?=(($loggedin)?'javascript:addfav()':'javascript:loginbox()')?>" class="favvideo" title="Add to your favorite video's" style="border-radius: 5px;">&hearts;</a>
					</p>
					<p>
						This video is:
						<a href="<?=(($loggedin)?'javascript:videoiscorrect()':'javascript:loginbox()')?>" class="favvideo" title="This video should be here.">Should be here!</a>
						<a class="reportvideo" href="<?=(($loggedin)?'javascript:reportwrong()':'javascript:loginbox()')?>" title="Remove this video from here!">Report wrong video</a>
					</p>
					<?php
						else:
					?>
					<p>
						<a href="<?=(($loggedin)?'javascript:addfav()':'javascript:loginbox()')?>" class="favvideo" title="Add to your favorite video's">&hearts;</a>
						<a class="reportvideo" href="<?=(($loggedin)?'javascript:reportwrong()':'javascript:loginbox()')?>" title="Remove this video from here!">Report wrong video</a>
					</p>
					<?php
						endif;
						if($curvid['channel_id'] != null):
					?>
					<h4>Channel '<?=$curvid['channel_name']?>'</h4>
					<p><a href="<?=(($loggedin)?'javascript:addtolist()':'javascript:loginbox()')?>" class="favvideo" title="Add channel to custom list">Add to list</a>
					<a class="reportvideo" href="<?=(($loggedin)?'javascript:dontshow()':'javascript:loginbox()')?>">Don't show again</a></p>
					<?php
						endif;
					?>
				</div>
			</div>
			<div class="clear"></div>
		</div>
		<div class="seperator"></div>
		<!-- video details --> */ ?>
		<article id="videodetails">
			<div class="wrap">
				<div class="item left">
					<div class="txt">
						<h1><span class="voting"><?php
								if($vote == null)
									echo '<span class="voteup vote"><i class="fa fa-plus-circle"></i></span> <span class="votedown vote"><i class="fa fa-minus-circle"></i></span>';
								else if($vote['type'] == '+')
									echo '<span class="upvoted vote"><i class="fa fa-plus-circle"></i></span>';
								else
									echo '<span class="downvoted vote"><i class="fa fa-minus-circle"></i></span>';
							?></span>
							<?=$curvid['title']?></h1>
						<p><b title="Added to youtubemonster at (CEST)"><?=date('j F, Y \a\t G:i', $curvid['addedon'])?></b></p>
						<div class="rating">
							<div class="scorebox">
								<span class="scoreline">Score: <span class="score videoscore"><?=($curvid['score'] > 0)?'+'.number_format($curvid['score'], 0, '', '.'):number_format($curvid['score'], 0, '', '.')?></span></span>
								<div class="votes"><span class="videovotes"><?=number_format($curvid['votes'], 0, '', '.')?></span> vote<span class="votess"><?=($curvid['votes'] == 1)?'':'s'?></span>, today <span class="videoscoretoday"><?=($curvid['scoretoday'] > 0)?'+'.number_format($curvid['scoretoday'], 0, '', '.'):number_format($curvid['scoretoday'], 0, '', '.')?></div>
							</div>
						</div>
						<h4><?=$curvid['channel_name']?></h4>
						<p><?php
							$text = trim(nl2br($curvid['content']));
							$text = preg_replace("/(?<!http:\/\/)www\./","http://www.",$text);
							$text = str_replace('https://http://', 'http://', $text);
							echo preg_replace( "/((http|ftp)+(s)?:\/\/[^<>\s]+)/i", "<a href=\"\\0\" target=\"_blank\" rel=\"nofollow\">\\0</a>",$text);
						?></p>
					</div>
				</div>
				
				<div class="item right">
					<div class="txt">
					
						<div id="disqus_thread"></div>
						<script type="text/javascript">
						/* * * CONFIGURATION VARIABLES: EDIT BEFORE PASTING INTO YOUR WEBPAGE * * */
						var disqus_shortname = 'youtubemonster';
						var disqus_identifier = '{shareurl}';

						/* * * DON'T EDIT BELOW THIS LINE * * */
						(function() {
						var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
						dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
						(document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
						})();
						</script>
						<noscript>Please enable JavaScript to view the <a href="http://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>

					</div>
				</div>
			</div>
			<div class="clear"></div>
		</article>
		<div class="seperator"></div>
		<script type="text/javascript">
			// ==============================
			// GLOBAL VARS
			// ==============================
			
			// GRAB YOUTUBE PLAYER
			// https://developers.google.com/youtube/iframe_api_reference
			var tag = document.createElement('script');
			tag.src = "//www.youtube.com/iframe_api";
			var firstScriptTag = document.getElementsByTagName('script')[0];
			firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
			
			// STEAMER VARS
			var startat = <?=(int)$curvid['startat']?>;
			var endat = <?=((int)$curvid['endat'] > 0)?(int)$curvid['endat']:999999999?>;
			var failcount = 0;
			var state = null;
			var timeat = null;
			var totaltime = null;
			var lasttime = '';
			var crashcount = 0;
			var nextvideo = null;
			var infullscreen = false;
			
			// ==============================
			// LOAD NEXT VIDEOS
			// ==============================
			var nextvids = [];
			var nextvidslasttime = 0;
			
			function showandsetnext(){
				if(nextvids.length > 0){
					var thisvid = nextvids.shift();
					$('#nextvideobox').html('<a href="' + thisvid['url'] + '"><div class="preview" style="background-image: url(\'http://img.youtube.com/vi/' + thisvid['tag'] + '/0.jpg\');"></div>' + thisvid['title'] + '</a><br /><br /><a class="skipnext" href="javascript:showandsetnext()">Skip next</a>');
					nextvidslasttime = thisvid['addedon'];
					nextvideo = thisvid['url'];
				}
				else{
					load10new(nextvidslasttime);
				}
			}
				
			function load10new(time){
				$('#nextvideobox').html('Loading next video...');
				$.ajax({
					url: '{base}ajax/nextvideos.php',
					type: 'post',
					dataType: 'json',
					data: {
						type: '<?=$_GET['page']?>',
						id: '<?=$GLOBALS['curpage']['id']?>',
						typeurl: '<?=$_GET['sub1']?>',
						videourl: '<?=$_GET['sub2']?>',
						modus: '<?=((empty($GLOBALS['playtype']))?'newtoold':$GLOBALS['playtype'])?>',
						curtime: time,
						nocache: new Date().getTime()
					},
					timeout: 30000,
					success: function(result){
						//alert('Grabbing next videos took: ' + result['took']);
						nextvids = result['nextvideos'];
						if(nextvids.length == 0){
							$('#nextvideobox').html('List completed, restarting.');
							nextvideo = '{base}<?=$_GET['page'].'/'.$_GET['sub1'].'/'?>';
						}
						else
							showandsetnext();
					},
					error: function(){
						$('#nextvideobox').html('Failed to load next video\'s. Trying again in 10 seconds.');
						setTimeout(load10new(time), 10000);
					}
				});
			}
			
			// ==============================
			// KEEP SESSION ALIVE
			// ==============================
			function defibrillateSession(){
				$.ajax({
					url: '{base}ajax/keepalive.php',
					data: {
						nocache: new Date().getTime()
					},
					timeout: 30000,
					success: function(){
						setTimeout(function(){
							defibrillateSession();
						}, 300000);
					},
					error: function(){
						setTimeout(function(){
							defibrillateSession();
						}, 5000);
					}
				});
			}
			setTimeout(function(){
				defibrillateSession();
			}, 300000);
			
			// ==============================
			// PLAYER JS
			// ==============================
			var player;
			function onYouTubeIframeAPIReady(){
				player = new YT.Player('player', {
					height: Math.round($('#playerwrapper').width() * 9 / 16),
					width: $('#playerwrapper').width(),
					videoId: '<?=$curvid['tag']?>',
					playerVars: {
						controls: 0,
						autoplay: 1,
						rel: 0,
						iv_load_policy: 3,
						showinfo: 0,
						wmode: 'opaque'
					},
					events: {
						'onReady': onPlayerReady
					}
				});
			}
			
			function setVolume(volume){
				player.setVolume(volume);
				$('#playercontrols .sound').css('height', volume + 'px');
				createCookie('volume', volume, 365);
				if(volume == 0)
					$('#playercontrols .volicon').html('<i class="fa fa-volume-off"></i>');
				else if(volume < 80)
					$('#playercontrols .volicon').html('<i class="fa fa-volume-down"></i>');
				else
					$('#playercontrols .volicon').html('<i class="fa fa-volume-up"></i>');
			}
			
			function onPlayerReady(event){
				if(startat > 0)
					player.seekTo(startat, true);
				statusChecker();
				player.setPlaybackQuality('hd720');
				var volume = readCookie('volume');
				if(volume == null || volume == '') volume = 50;
				setVolume(volume);
			}
				
			function totime(seconds){
				seconds = Math.round(seconds);
				var result = '';
				if(Math.floor(seconds / 3600) > 0){
					var hours = Math.floor(seconds / 3600);
					result += hours + ':';
					seconds = seconds - (hours * 3600);
				}
				var minutes = hours = Math.floor(seconds / 60);
				if(minutes < 10) result += '0';
				result += minutes + ':';
				seconds = seconds - (minutes * 60);
				if(seconds < 10) result += '0';
				result += seconds;
				return result;
			}
			
			$(window).resize(function(){
				if(!infullscreen)
					player.setSize($('#videobox').width(), Math.round($('#videobox').width() * 9 / 16));
			});
			
			function toNextVideo(){
				if($('#popupbox').css('display') == 'block'){
					$('#playerstatus').html('[NEXT] Wait for popup to close...').css('display', 'block');
					setTimeout(function(){ toNextVideo() }, 500);
				}
				else if(nextvideo == null){
					$('#playerstatus').html('[NEXT] no next video was loaded yet...').css('display', 'block');
					setTimeout(function(){ toNextVideo() }, 500);
				}
				else{
					window.location = nextvideo;
				}
			}
			
			function statusChecker(){
				// Grab player vars
				state = player.getPlayerState();
				timeat = player.getCurrentTime();
				totaltime = player.getDuration();
				
				// Timebar
				if(totaltime > 0)
					$('#playercontrols .progress').stop().animate({
						width: (timeat / totaltime * 100) + '%'
					}, {
						easing: 'linear',
						duration: 1000
					});
				
				// Time display
				$('#playerstime').html(totime(timeat));
				$('#playeretime').html(totime(totaltime));
				
				// Prevent failure videos
				if(state == -1 || state == 3)
					failcount++;
				else
					failcount = 0;
					
				// Set player icon
				var crashing = false;
				if(state == 1){
					if(lasttime == totime(timeat)){
						crashcount++;
						if(crashcount > 2){
							crashing = true;
							$('#playerstatus').html('Player crash detected... ' + crashcount + '/10').css('display', 'block');
						}
					}
					else
						crashcount = 0;
					$('#playercontrols .playbtn').css('display', 'none');
					$('#playercontrols .pausebtn').css('display', 'block');
					if(!crashing)
						$('#playerstatus').css('display', 'none');
					lasttime = totime(timeat);
				}
				else{
					$('#playercontrols .playbtn').css('display', 'block');
					$('#playercontrols .pausebtn').css('display', 'none');
					switch(state){
						case -1: $('#playerstatus').html('Waiting for start... ' + failcount + '/10').css('display', 'block'); break;
						case 0: $('#playerstatus').html('Video ended. Next.').css('display', 'block'); break;
						case 2: $('#playerstatus').html('Paused.').css('display', 'block'); break;
						case 3: $('#playerstatus').html('Buffering... ' + failcount + '/20').css('display', 'block'); break;
						case 5: $('#playerstatus').html('Autostarting... (might not work on mobile devices)').css('display', 'block'); break;
					}
				}
				
				// Video failed, moving on...
				if((failcount > 9 && state == -1) || (failcount > 19 && state == 3) || (crashcount > 9)){
					toNextVideo();
					return;
				}
				
				// Check if video is done to move on
				if(state == 0 || timeat > endat){
					toNextVideo();
				}
				// If not done visit again soon
				else{
					setTimeout(function(){
						statusChecker();
					}, 1000);
				}
			}
			
			$(function(){
				// ===== Load next video's ===== //
				load10new(<?=$curvid['addedon']?>);
				
				// ==============================
				// Player controls
				// ==============================
				$('#playbtn').click(function(){
					if(totaltime > 0){
						player.playVideo();
						$('#playercontrols .playbtn').css('display', 'none');
						$('#playercontrols .pausebtn').css('display', 'block');
					}
				});
				$('#pausebtn').click(function(){
					if(totaltime > 0){
						player.pauseVideo();
						$('#playercontrols .playbtn').css('display', 'block');
						$('#playercontrols .pausebtn').css('display', 'none');
					}
				});
				/*$('#soundbtn').click(function(){
					if(totaltime > 0){
						if(player.getVolume() == 0){
							setVolume(50);
						}
						else{
							setVolume(0);
						}
					}
				});*/
				
				$('#clicktimearea').click(function(e){
					var clickat = Math.round(e.pageX - $(this).offset().left);
					var barw = $('#clicktimearea').width();
					$('#playercontrols .progress').stop().css('width', (clickat / barw * 100) + '%');
					player.seekTo(Math.round(clickat / barw * player.getDuration()), true);
				});
				$('#clicksoundarea').click(function(e){
					var clickat = Math.round(e.pageY - $(this).offset().top);
					clickat = 115 - clickat;
					if(clickat > 100) clickat = 100;
					if(clickat < 0) clickat = 0;
					setVolume(clickat);
				});
				
				// ==============================
				// Fullscreen
				// ==============================
				$('#fullscreen').click(function(){
					if(screenfull.enabled)
						screenfull.toggle($('#playerwrapper')[0]);
				});
				document.addEventListener(screenfull.raw.fullscreenchange, function(){
					$('#fullscreen').css('background-color', 'transparent');
					if(!screenfull.isFullscreen){
						var
							w = $('.wrap').width(),
							h = Math.round($('.wrap').width() * 9 / 16);
						$('#playerwrapper').css({
							width: w + 'px',
							height: h + 49 + 'px'
						});
						$('#playercontrols').stop(true, true).css({
							position: 'relative',
							'border-radius': '0px 0px 4px 4px',
							bottom: '0px'
						});
						player.setSize(w, h);
						$('#fullscreen i').removeClass('fa-compress').addClass('fa-arrows-alt');
						infullscreen = false;
					}
					else{
						$('#playerwrapper').css({
							width: screen.width,
							height: screen.height
						});
						player.setSize(screen.width, screen.height);
						$('#playercontrols').css({
							position: 'absolute',
							'border-radius': '0px'
						});
						$('#fullscreen i').removeClass('fa-arrows-alt').addClass('fa-compress');
						infullscreen = true;
						$('#playercontrols').animate({
							bottom: '-50px'
						}, 1000);
						toolsopen = false;
					}
				});
				var toolsopen = false;
				$(document).mousemove(function(loc){
					if(infullscreen){
						if(loc.pageY > $(window).height() - 300){
							if(!toolsopen)
								$('#playercontrols').stop().animate({
									bottom: '0px'
								}, 500);
							toolsopen = true;
						}
						else{
							if(toolsopen)
								$('#playercontrols').stop().animate({
									bottom: '-50px'
								}, 500);
							toolsopen = false;
						}
					}
				});
				var lastclick = null;
				$('#fullscreenhitzone').click(function(){
					now = new Date().getTime();
					if(lastclick > now - 500)
						screenfull.toggle($('#playerwrapper')[0]);
					lastclick = now;
				});
				
				// ==============================
				// Player tools details
				// ==============================
				$('#playercontrols .pbtn').hover(function(){
					$(this).children('.hoverbox, .icon').css('display', 'block').stop().fadeTo(300, 1);
				}, function(){
					$(this).children('.hoverbox, .icon').stop().fadeTo(300, 0, function(){
						$(this).css('display', 'none');
					});
				});
				/*$('#playercontrols .pbtn').click(function(){
					$(this).children('.hoverbox, .icon').stop().fadeToggle(300);
				});*/
			<?php
				if($vote == null){
			?>
				var votecast = false;
				function vote(videoid, type){
					if(votecast) return;
					votecast = true;
					if(type == '+')
						$('.voting').html('<span class="upvoted vote"><i class="fa fa-plus-circle"></i> </span>');
					else
						$('.voting').html('<span class="downvoted vote"><i class="fa fa-minus-circle"></i> </span>');
					$.ajax({
						url: '{base}ajax/ratevideo.php',
						type: 'post',
						data: {
							videoid: videoid,
							type: type
						},
						dataType: 'json',
						success: function(result){
							$('.videoscore').html(result['score']);
							$('.videoscoretoday').html(result['scoretoday']);
							$('.videovotes').html(result['votes']);
							$('.votess').html((result['votes'] == 1)?'':'s');
						}
					});
				}
				$('.voteup').click(function(){
					vote(<?=$curvid['id']?>, '+');
				});
				$('.votedown').click(function(){
					vote(<?=$curvid['id']?>, '-');
				});
			<?php
				}
			?>
			});
		</script>