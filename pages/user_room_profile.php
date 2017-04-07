<?php
	$x7->load('user');
	$db = $x7->db();
	
	if(empty($_SESSION['user_id']))
	{
		$x7->fatal_error($x7->lang('login_required'));
	}
	
	$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : 0;
	$user = x7_get_user($user_id);
	
	$show_ip = false;
	$allow_ban = false;
	$perms = x7_get_user_permissions();
	if($perms)
	{
		$show_ip = $perms['access_admin_panel'];
		$allow_ban = $perms['access_admin_panel'];
	}
	
	$x7->display('pages/user_room_profile', array(
		'user' => $user,
		'show_ip' => $show_ip,
		'allow_ban' => $allow_ban
	));