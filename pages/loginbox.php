		<div id="popupbox">
			<div class="boxwrap">
				<div class="box">
					<div class="closebtn">X</div>
					<h1>Login required</h1>
					<div class="content">
						<p class="error"></p>
						<p>
							<b>Username</b><br />
							<input class="input" id="loginusername" type="text" placeholder="Username" />
						</p>
						<p class="register">
							<b>Email address</b><br />
							<input class="input" id="loginemail" type="text" placeholder="Email address" />
						</p>
						<p>
							<b>Password</b><br />
							<input class="input" id="loginpassword" type="password" placeholder="P&#9679;ssw&#9679;rd" />
						</p>
						<p class="activation">
							<b>Activation code</b><br />
							<input class="input" id="activatekey" type="text" placeholder="00000" />
						</p>
						<p class="register">
							<b>Repeat password</b><br />
							<input class="input" id="loginrepeatpassword" type="password" placeholder="R&#9679;peat p&#9679;ssw&#9679;rd" />
						</p>
						<p class="login">
							<label><input type="checkbox" id="loginremember" /> Remember me</label>
						</p>
						<p class="loginquick">
							<button class="button" id="loginnow">Log in</button>
						</p>
						<p class="registerquick">
							<button class="button" id="registernow">Register</button>
						</p>
						<p class="loginquick" id="registerbtn">
							<button class="button">No account yet? Register!</button>
						</p>
						<p class="registerquick" id="loginbtn">
							<button class="button">Oops, just login!</button>
						</p>
						<p>
							I forgot my <a href="./forgot-username/">username</a>/<a href="./forgot-password/">password</a>...
						</p>
					</div>
				</div>
			</div>
		</div>
		<script type="text/javascript">
			var logintarget = null;
			var loggingin = false;
			var registering = false;
		
			$(document).ready(function(){
				function error(msg){
					$('#popupbox .error').html(msg);
					$('#popupbox .error').slideDown(500, function(){
						setTimeout(function(){
							$('#popupbox .error').slideUp(500);
						}, 5000);
					});
				}
				function showlogin(){
					$('.login').slideDown(1000);
					$('.register').slideUp(1000);
					$('.registerquick').css('display', 'none');
					$('.loginquick').css('display', 'block');
				}
				$('#loginnow').click(function(){
					if(loggingin) return;
					loggingin = true;
					$('#loginnow').html('Logging in...');
					$.ajax({
						url: '{base}ajax/login.php',
						data: {
							username: $('#loginusername').val(),
							password: $('#loginpassword').val(),
							remember: $('#loginremember').is(':checked')?'yes':'no',
							activatekey: $('#activatekey').val(),
							nocache: new Date().getTime()
						},
						type: 'post',
						postType: 'html',
						timeout: 30000,
						success: function(callback){
							switch(callback){
								case 'success':
									if(logintarget == null)
										setTimeout(function(){ location.reload(); }, 500);
									else
										setTimeout(function(){ window.location = '{base}' + logintarget + '/'; }, 500);
								break;
								case 'activatetoken':
									$('.activation').slideDown(1000);
									error('Please insert the activate key you received in your mail to activate your account.');
									$('#loginnow').html('Log in');
								break;
								default:
									error(callback);
									$('#loginnow').html('Log in');
								break;
							}
							loggingin = false;
						},
						error: function(){
							error('Could not log in, please check your internet connection.');
							$('#loginnow').html('Log in');
							loggingin = false;
						}
					});
				});
				$('#registernow').click(function(){
					if(registering) return;
					registering = true;
					$('#registernow').html('Registering...');
					$.ajax({
						url: '{base}ajax/register.php',
						data: {
							username: $('#loginusername').val(),
							password: $('#loginpassword').val(),
							password2: $('#loginrepeatpassword').val(),
							email: $('#loginemail').val(),
							nocache: new Date().getTime()
						},
						type: 'post',
						postType: 'html',
						timeout: 30000,
						success: function(callback){
							switch(callback){
								case 'success':
									error('<span style="color: #00D000;">Log in using the activation token in your email</span>');
									showlogin();
									$('.activation').slideDown(1000);
								break;
								default:
									error(callback);
								break;
							}
							$('#registernow').html('Register');
							registering = false;
						},
						error: function(){
							$('#registernow').html('Register');
							error('Could not log in, please check your internet connection.');
							registering = false;
						}
					});
				});
				$('#popupbox .closebtn').click(function(){
					$('#popupbox').fadeTo(400, 0, function(){
						$('#popupbox').css('display', 'none');
					});
				});
				$('#registerbtn').click(function(){
					$('.login').slideUp(1000);
					$('.register').slideDown(1000);
					$('.loginquick').css('display', 'none');
					$('.registerquick').css('display', 'block');
				});
				$('#loginbtn').click(function(){
					showlogin();
				});
			});
				
			function loginbox(target){
				logintarget = target;
				$('#popupbox').fadeTo(400, 1);
			}
		</script>