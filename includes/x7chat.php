<?php

	function vals()
	{
		$args = func_get_args();
		$source = $args[0];
		$output = array();
		for($index = 1; $index < count($args); $index++)
		{
			$output[] = isset($source[$args[$index]]) ? $source[$args[$index]] : null;
		}
		return $output;
	}

	function val($array, $index)
	{
		return isset($array[$index]) ? $array[$index] : null;
	}

	class x7chat
	{
		const VERSION = '3.3.0a2';
		const VERSION_ID = 30300102;
	
		protected $strings;
		protected $db;
		protected $config;
		public $dbprefix;
	
		public function __construct()
		{
			session_start();

			$config = require('./config.php');

			if(!defined('cost')) {

				define('cost', $config['cost']);

			}

		}
		
		public function load($lib)
		{
			require_once('./includes/' . $lib . '.php');
		}
		
		public function fatal_error($error)
		{
			die($error);
		}
		
		public function db()
		{
			if(!$this->db)
			{
				$config = require('./config.php');
				
				$dsn = 'mysql:host=' . $config['host'] . ';dbname=' . $config['dbname'] . ';charset=utf8';
				$options = array(
					PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
					PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => FALSE,
				);
				$db = new PDO($dsn, $config['user'], $config['pass'], $options);
				$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$db->setAttribute(PDO::ATTR_AUTOCOMMIT, TRUE);
				$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
				
				$this->db = $db;
				$this->dbprefix = $config['prefix'];
			}
			
			return $this->db;
		}
		
		public function url($page)
		{
			$page = preg_replace("#^([a-z0-9_-]+)\?#i", '$1&', $page);
			$host = $_SERVER['HTTP_HOST'];
			$mode = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
			$path = $_SERVER['REQUEST_URI'];
			$url = parse_url($mode . $host . $path);
			$req_url = $url['scheme'] . '://' . $url['host'] . $url['path'] . '?page=' . $page;
			return $req_url;
		}
		
		public function go($to, $vars = array())
		{
			$to = preg_replace("#^([a-z0-9_-]+)\?#i", '$1&', $to);
			$_SESSION['vars'] = $vars;
			header("Location: ?page=$to");
			session_write_close();
			exit;
		}
		
		public function get_vars($clear = true)
		{
			$vars = isset($_SESSION['vars']) ? $_SESSION['vars'] : array();
			
			if($clear === true)
			{
				unset($_SESSION['vars']);
			}
			
			return $vars;
		}
		
		public function post($var)
		{
			return isset($_POST[$var]) ? $_POST[$var] : null;
		}
		
		public function config($var)
		{
			if(!$this->config)
			{
				$db = $this->db();
				$sql = "SELECT * FROM {$this->dbprefix}config LIMIT 1";
				$st = $db->prepare($sql);
				$st->execute();
				$row = $st->fetch();
				$this->config = $row;
			}
			
			if(isset($this->config[$var]))
			{
				return $this->config[$var];
			}
			else
			{
				return null;
			}
		}

		public function lang($string, $vars = array())
		{
			if(empty($this->strings))
			{
				$this->strings = require('./languages/en-us.php');
			}
			
			$string = isset($this->strings[$string]) ? $this->strings[$string] : 'MISSING TRANSLATION: ' . $string;
			
			foreach($vars as $key => $value)
			{
				$string = str_replace($key, $value, $string);
			}
			
			return $string;
		}
		
		public function tl($string)
		{
			if(empty($this->strings))
			{
				require('./languages/en-us.php');
			}
			
			echo isset($this->strings[$string]) ? $this->strings[$string] : 'MISSING TRANSLATION: ' . $string;
		}
		
		public function set_message($message, $type = 'error')
		{
			$_SESSION['messages'][$type][] = $message;
		}
		
		public function get_messages($type, $clear = true)
		{
			$messages = isset($_SESSION['messages'][$type]) ? $_SESSION['messages'][$type] : array();
		
			if($clear)
			{
				unset($_SESSION['messages'][$type]);
			}
			
			return $messages;
		}
		
		public function esc($var)
		{
			return htmlentities($var, ENT_QUOTES, 'UTF-8');
		}
		
		public function render($template, $vars = array())
		{
			ob_start();
			$this->display($template, $vars);
			$contents = ob_get_contents();
			ob_end_clean();
			return $contents;
		}
	
		public function display($template, $vars = array())
		{
			$x7 = $this;
			
			$vars = array_merge($this->get_vars(), $vars);
			extract($vars);
			
			$val = function($var) use($vars)
			{
				$inspect = &$vars;
				$parts = explode('.', $var);
				do {
					$part = array_shift($parts);
					if(!isset($inspect[$part]))
					{
						return null;
					}
					$inspect = &$inspect[$part];
				} while($parts);
				
				return $inspect;
			};
			
			$var = function($var) use($vars, $val)
			{
				echo $val($var);
			};
			
			$display = function($template, $extra_vars = array()) use($x7, $vars)
			{
				return $x7->display($template, array_merge($vars, $extra_vars));
			};
			
			$esc = function($value)
			{
				echo htmlentities($value, ENT_QUOTES, 'UTF-8');
			};
			
			$lang = function($string, $vars = array()) use($x7)
			{
				echo $x7->lang($string, $vars);
			};
			
			$url = function($string) use($x7)
			{
				echo $x7->url($string);
			};
			
			require('./templates/default/' . $template . '.php');
		}
	}
