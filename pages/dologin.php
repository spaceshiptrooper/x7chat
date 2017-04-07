<?php
	$x7->load('user');

	$db = $x7->db();
	
	$username = isset($_POST['username']) ? $_POST['username'] : null;
	if(!$username)
	{
		$x7->set_message($x7->lang('missing_login_username'));
		$x7->go('login');
	}
	
	// Check IP bans
	$ban_match = x7_check_ip_bans();
	if($ban_match)
	{
		$x7->set_message($x7->lang('login_failed_banned'));
		$x7->go('login');
	}
	
	$password = isset($_POST['password']) ? $_POST['password'] : null;
	
	try
	{
		$user_ob = new x7_user($username, 'username');
		$user = $user_ob->data();
	}
	catch(x7_exception $ex)
	{
		$user = array();
	}

	if($user && !$user['password'] && strtotime($user['timestamp']) < time() - 120)
	{
		$sql = "DELETE FROM {$x7->dbprefix}users WHERE id = :del_id";
		$st = $db->prepare($sql);
		$st->execute(array(':del_id' => $user['id']));
		$user = array();
	}
	
	if(!$user && $x7->config('allow_guests'))
	{
		$sql = "INSERT INTO {$x7->dbprefix}users (username, email) VALUES (:username, :email)";
		$st = $db->prepare($sql);
		$st->execute(array(':username' => $username, ':email' => $username));
		$user_id = $db->lastInsertId();
		$user = array('id' => $user_id);
	}
	elseif(!$user || !$user['password'] || !password_verify($password, $user['password']))
	{
		$x7->set_message($x7->lang('login_failed'));
		$x7->go('login', array('username' => $username));
	}
	
	// Check user ID bans
	if($user_ob->banned())
	{
		$x7->set_message($x7->lang('login_failed_banned'));
		$x7->go('login');
	}
	
	$_SESSION['user_id'] = $user['id'];
	
	$x7->go('chat');