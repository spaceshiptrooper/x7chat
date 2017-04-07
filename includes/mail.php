<?php

	class x7_mail
	{
		protected $x7;
		protected $mailer;
	
		public function __construct()
		{
			global $x7;
			$this->x7 = $x7;
			
			require_once('./includes/libraries/swift/swift_required.php');
			
			$transport = null;
			if($this->x7->config('use_smtp'))
			{
				$host = $this->x7->config('smtp_host');
				$port = $this->x7->config('smtp_port');
				$username = $this->x7->config('smtp_user');
				$password = $this->x7->config('smtp_pass');
				$mode = $this->x7->config('smtp_mode');
				$transport = Swift_SmtpTransport::newInstance($host, $port, $mode)
					->setUsername($username)
					->setPassword($password);
			}
			else
			{
				$transport = Swift_MailTransport::newInstance();
			}
			
			$this->mailer = Swift_Mailer::newInstance($transport);
			
			//$logger = new Swift_Plugins_Loggers_EchoLogger();
			//$this->mailer->registerPlugin(new Swift_Plugins_LoggerPlugin($logger));
		}
		
		public function send($to, $template, $vars)
		{
			$subject = preg_replace("#[\r\n]#", '', $this->x7->render('emails/' . $template . '/' . $template . '_subject', $vars));
			$html = $this->x7->render('emails/' . $template . '/' . $template . '_html', $vars);
			$text = strip_tags($this->x7->render('emails/' . $template . '/' . $template . '_text', $vars));
			
			$message = Swift_Message::newInstance($subject)
				->setFrom($this->x7->config('from_address'))
				->setTo($to)
				->setBody($text)
				->addPart($html, 'text/html');
			
			return $this->mailer->send($message);
		}
	}