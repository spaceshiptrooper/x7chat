<?php
	$x7->load('user');
	
	$db = $x7->db();
	
	if(empty($_SESSION['user_id']))
	{
		$x7->fatal_error($x7->lang('login_required'));
	}
	
	$user = new x7_user();
	$perms = $user->permissions();
	if(empty($perms['access_admin_panel']))
	{
		$x7->fatal_error($x7->lang('access_denied'));
	}
	
	$config = array();
	$error = false;
	
	if(empty($_POST['title']))
	{
		$x7->set_message($x7->lang('title_required'));
		$error = true;
	}
	else
	{
		$config['title'] = $_POST['title'];
	}

	$config['chat_size'] = isset($_POST['chat_size']) ? $_POST['chat_size'] : 0;
	if($_POST['chat_size'] == 0) {

		$config['chat_size'] = 'boxed';

	} elseif($_POST['chat_size'] == 1) {

		$config['chat_size'] = 'full';

	}

	$config['auto_join'] = isset($_POST['auto_join']) ? $_POST['auto_join'] : 0;
	$config['allow_guests'] = isset($_POST['allow_guests']) ? 1 : 0;
	
	if(!filter_var($_POST['from_address'], FILTER_VALIDATE_EMAIL))
	{
		$x7->set_message($x7->lang('invalid_from_address'));
		$error = true;
	}
	else
	{
		$config['from_address'] = $_POST['from_address'];
	}
	
	$config['use_smtp'] = isset($_POST['use_smtp']) ? 1 : 0;
	
	$config['smtp_host'] = isset($_POST['smtp_host']) ? $_POST['smtp_host'] : '';
	if($config['use_smtp'] && !$config['smtp_host'])
	{
		$x7->set_message($x7->lang('smtp_host_required'));
		$error = true;
	}
	
	$config['smtp_port'] = isset($_POST['smtp_port']) ? (int)$_POST['smtp_port'] : 0;
	if($config['use_smtp'] && !$config['smtp_port'])
	{
		$x7->set_message($x7->lang('smtp_port_required'));
		$error = true;
	}
	
	$config['smtp_user'] = isset($_POST['smtp_user']) ? $_POST['smtp_user'] : '';
	$config['smtp_pass'] = isset($_POST['smtp_pass']) ? $_POST['smtp_pass'] : '';
	$config['smtp_mode'] = isset($_POST['smtp_mode']) ? $_POST['smtp_mode'] : '';
	
	$config['login_page_news'] = isset($_POST['login_page_news']) ? $_POST['login_page_news'] : '';
	
	$config['min_font_size'] = isset($_POST['min_font_size']) ? (int)$_POST['min_font_size'] : '';
	$config['max_font_size'] = isset($_POST['max_font_size']) ? (int)$_POST['max_font_size'] : '';
	
	if($config['min_font_size'] > $config['max_font_size'])
	{
		$x7->set_message($x7->lang('invalid_min_max_font_sizes'));
		$error = true;
	}
	
	if($config['min_font_size'] < 1)
	{
		$x7->set_message($x7->lang('invalid_min_font_sizes'));
		$error = true;
	}
	
	if($config['max_font_size'] > 100)
	{
		$x7->set_message($x7->lang('invalid_max_font_sizes'));
		$error = true;
	}
	
	if($error)
	{
		$x7->go('admin_settings', array('config' => $_POST));
	}
	else
	{
		$sql = "
			UPDATE {$x7->dbprefix}config SET
				title = :chatroom_title,
				auto_join = :auto_join,
				chat_size = :chat_size,
				use_smtp = :use_smtp,
				smtp_host = :smtp_host,
				smtp_user = :smtp_user,
				smtp_pass = :smtp_pass,
				smtp_port = :smtp_port,
				smtp_mode = :smtp_mode,
				from_address = :from_address,
				allow_guests = :allow_guests,
				min_font_size = :min_font_size,
				max_font_size = :max_font_size,
				login_page_news = :login_page_news
			LIMIT 1;
		";
		$st = $db->prepare($sql);
		$st->execute(array(
			':chatroom_title' => $config['title'],
			':auto_join' => $config['auto_join'],
			':chat_size' => $config['chat_size'],
			':use_smtp' => $config['use_smtp'],
			':smtp_host' => $config['smtp_host'],
			':smtp_user' => $config['smtp_user'],
			':smtp_pass' => $config['smtp_pass'],
			':smtp_port' => $config['smtp_port'],
			':smtp_mode' => $config['smtp_mode'],
			':from_address' => $config['from_address'],
			':allow_guests' => $config['allow_guests'],
			':min_font_size' => $config['min_font_size'],
			':max_font_size' => $config['max_font_size'],
			':login_page_news' => $config['login_page_news'],
		));
	
		$x7->set_message($x7->lang('admin_settings_updated'), 'notice');
		$x7->go('admin_settings');
	}