<?php
	# ===============================
	# MultiMailer is created to send
	# e-mails in the current language
	# ===============================
	class templatemail{
	
		# ================================
		# Variables
		# ================================
		private $mailer, $content, $subject, $receivers, $unsubscribelink, $language;
	
		# ================================
		# Constructz0r & main setup
		# Receivers: Array(e-mail => name)
		# ================================
		function __construct($content, $subject, $receivers){
			# Grab global config
			global $config;
		
			# Setup mailer
			$this->mailer = new mailer();
			$this->mailer->IsSMTP();
			$this->mailer->SMTPDebug = 0;
			$this->mailer->SMTPAuth = true;
			$this->mailer->SMTPSecure = $config['mail']['SMTPSecure'];
			$this->mailer->Host = $config['mail']['Host'];
			$this->mailer->Port = $config['mail']['Port'];
			$this->mailer->IsHTML(true);
			$this->mailer->Username = $config['mail']['Username'];
			$this->mailer->Password = $config['mail']['Password'];
			
			# Content from multilanguage table
			$this->content = $content;

			# Subject from multilanguage table
			if(empty($subject)) $subject = 'No subject';
			$this->subject = $subject.' | '.$config['domaintitle'];
			
			# Set receivers
			$this->receivers = $receivers;
			foreach($receivers as $email => $name)
				$this->mailer->AddAddress($email, $name);
			
			# Set from address (USE CURRENT DOMAIN INFO
			$this->mailer->SetFrom($config['mail']['SendFromAddress'], $config['mail']['SendFromName']);
			$this->mailer->FromName = $config['mail']['SendFromName'];
			
			# Send copy
			$this->mailer->AddBCC($config['mail']['SendFromAddress'], $config['mail']['SendFromName']);
		}
		
		# ================================
		# Set unsubscribe link
		# ================================
		public function unsubscribelink($url){
			$this->unsubscribelink = $url;
		}
		
		# ================================
		# Echo a preview of the mail
		# ================================
		private function htmlmail(){
			# Load email template
			$mail = new template('email.html');
		
			# Set unsubscibe link or system message
			if(empty($this->$unsubscribelink))
				$mail->setcontent('notice', 'This is a system message, therefor you can\'t unsubscribe yourself of this e-mail.');
			else
				$mail->setcontent('notice', '<a href="'.$this->unsubscribelink().'">Unsubscribe from this mailing</a>');
			
			# Replace content
			$mail->setcontent('message', $this->content);
			$mail->setcontent('title', $this->subject);
			$mail->setcontent('headertitle', $GLOBALS['domain']['title']);
			
			# Return result
			$mail->setcontent('baseurl', $GLOBALS['config']['baseurl']);
			$mail->setcontent('templateroot', 'templates/'.$GLOBALS['config']['template'].'/');
			return $mail->display();
		}
		
		# ================================
		# Echo a preview of the mail
		# ================================
		public function preview(){
			return '
			<h1>Preview of the current mail</h1>
			<p>Subject: '.$this->subject.'</p>
			<p><pre>Sending mail to <b>[email] => name</b>:
'.print_r($this->receivers, true).'</pre></p>
			'.$this->htmlmail();
		}
		
		# ================================
		# Send the mail, just like that
		# ================================
		public function send(){
			# Set alternative html text
			$this->mailer->AltBody = strip_tags($this->content);
			
			# Set last stuff
			$this->mailer->MsgHTML($this->htmlmail());
			$this->mailer->Subject = $this->subject;
			
			# Send it bro
			return $this->mailer->Send();
		}
	
	}