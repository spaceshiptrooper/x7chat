<?php

	date_default_timezone_set('UTC');
	
	error_reporting(E_ALL);
	ini_set('display_errors', 'on');

	if(PHP_VERSION < '5.4.9') {

		// Only need the alternative password file if the PHP version is 5.4.9 and below
		// password_hash is available in 5.5 so this file is not really needed.
		require('./includes/libraries/password_compat/lib/password.php');

	}

	$config = require('./config.php');
	if(!is_array($config) || empty($config['dbname']))
	{
		header('Location: ./install/index.php');
		die("Redirecting to install/index.php");
	} else {
		function rrmdir($dir) {
			if(is_dir($dir)) {
				$objects = scandir($dir);
				foreach($objects as $object) {
					if($object != '.' && $object != '..') {
						if(is_dir($dir . '/' . $object)) {
							rrmdir($dir . '/' . $object);
						} else {
							unlink($dir . '/' . $object);
						}
					}
				}
				rmdir($dir);
			}
		}

		rrmdir('./install/');
	}
	
	require('./includes/x7chat.php');
	$x7 = new x7chat;

	$page = isset($_GET['page']) ? $_GET['page'] : 'chat';
	if(preg_match('#[^a-z0-9_]#', $page) || !file_exists('./pages/' . $page . '.php')) {
		throw new exception('Invalid page');
	}
	
	if(!in_array($page, array('sync', 'login', 'dologin')))
	{
		$x7->load('user');
		try
		{
			$user = new x7_user();
			$user_banned = $user->banned();
		}
		catch(x7_exception $ex)
		{
			$user_banned = false;
			
			if(!empty($_SESSION['user_id']))
			{
				$_SESSION = array();
				session_destroy();
				$x7->go('login');
			}
		}
		
		if(x7_check_ip_bans() || $user_banned)
		{
			$x7->set_message($x7->lang('login_failed_banned'));
			$x7->go('login');
		}
	}
	
	require('./pages/' . $page . '.php');