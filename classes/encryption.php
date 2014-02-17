<?php
	# ===========================================
	# Encryption v1.1
	# Created by:
	#   Rick van der Staaij
	#   May 2012
	# ===========================================
	class encryption {
		# Salt
		private
			$salt = '9f8awh08fa7h',
			$rounds = 1582;
		
		function __construct(){
			global $config;
			$this->salt = $config['password']['salt'];
			$this->rounds = $config['password']['rounds'];
		}
		
		# Token generator
		public function generatetoken($length = 50, $chars = '') {
			if(empty($chars))
				$chars = 'abcdefghijklmnopqrstuvwqyz!,-+=';
			for($i = 1; $i <= $length; $i++)
				$token .= $chars[rand(0, strlen($chars) - 1)];
			return $token;
		}
		
		# Decrypter
		public function decrypt($encrypted_text, $salt = '') {
			$decoded = base64_decode($encrypted_text);
			$td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_ECB, '');
			$iv = pack("a" . mcrypt_enc_get_iv_size($td), NULL);
			mcrypt_generic_init($td, $this->salt.$salt, $iv);
			$decrypted = mdecrypt_generic($td, $decoded);
			mcrypt_generic_deinit($td);
			mcrypt_module_close($td);
			return preg_replace('/[^a-zA-Z0-9\_\-\!\?\&\\\%\&\*\(\)\<\>\[\]\'\"\.\,]/', '', rtrim($decrypted)) ;
		}
		
		# Encrypter
		public function encrypt($plain_text, $salt = '') {
			$td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_ECB, '');
			$iv = pack("a" . mcrypt_enc_get_iv_size($td), NULL);
			mcrypt_generic_init($td, $this->salt.$salt, $iv);
			$blocksize = mcrypt_enc_get_block_size($td);
			$plain_text = self::pkcs5_pad($plain_text, $blocksize);
			$encrypted = mcrypt_generic($td, $plain_text);
			$encoded = base64_encode($encrypted);
			mcrypt_generic_deinit($td);
			mcrypt_module_close($td);
			return $encoded;
		}
		private function pkcs5_pad ($text, $blocksize) {
			$pad = $blocksize - (strlen($text) % $blocksize);
			return $text . str_repeat(chr($pad), $pad);
		}
	
		# Safe password storage
		function safepass($str, $salt) {
			$salt = '$6$rounds='.$this->rounds.'$'.strtolower($salt).$this->salt.'$';
			return str_replace($salt, '', crypt($str, $salt));
		}
	}